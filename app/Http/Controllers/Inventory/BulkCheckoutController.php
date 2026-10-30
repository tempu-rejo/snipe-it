<?php

namespace App\Http\Controllers\Inventory;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\CustomField;
use App\Models\EventChecklist;
use App\Models\Location;
use App\Models\Statuslabel;
use App\Models\ToolTransaction;
use App\Models\ToolTransactionItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Services\EventChecklistService;
use \Illuminate\Contracts\View\View;
use \Illuminate\Http\RedirectResponse;

class BulkCheckoutController extends Controller
{
    /**
     * Display a listing of checkout transactions.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Asset::class);

        return view('inventory.bulk-checkout.index');
    }

    /**
     * Get data for DataTables
     */
    public function data(Request $request)
    {
        $this->authorize('viewAny', Asset::class);

        $query = ToolTransaction::checkout()
            ->with(['user', 'createdBy', 'items.asset']);

        // Search functionality
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('document_number', 'LIKE', "%{$search}%")
                  ->orWhere('notes', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('first_name', 'LIKE', "%{$search}%")
                               ->orWhere('last_name', 'LIKE', "%{$search}%")
                               ->orWhere('username', 'LIKE', "%{$search}%")
                               ->orWhere('employee_num', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('createdBy', function($creatorQuery) use ($search) {
                      $creatorQuery->where('first_name', 'LIKE', "%{$search}%")
                                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                                  ->orWhere('username', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Sorting
        $sort = $request->get('sort', 'created_at');
        $order = $request->get('order', 'desc');

        switch ($sort) {
            case 'document_number':
                $query->orderBy('document_number', $order);
                break;
            case 'transaction_date':
                $query->orderBy('transaction_date', $order);
                break;
            case 'user_name':
                $query->leftJoin('users as checkout_users', 'tool_transactions.user_id', '=', 'checkout_users.id')
                      ->orderBy('checkout_users.last_name', $order)
                      ->orderBy('checkout_users.first_name', $order)
                      ->select('tool_transactions.*');
                break;
            case 'created_by_name':
                $query->leftJoin('users as creator_users', 'tool_transactions.created_by', '=', 'creator_users.id')
                      ->orderBy('creator_users.last_name', $order)
                      ->orderBy('creator_users.first_name', $order)
                      ->select('tool_transactions.*');
                break;
            default:
                $query->orderBy('created_at', $order);
                break;
        }

        $transactions = $query->get();

        $rows = [];
        foreach ($transactions as $transaction) {
            $rows[] = [
                'id' => $transaction->id,
                'document_number' => '<a href="' . route('inventory.bulk-checkout.show', $transaction->id) . '">' . $transaction->document_number . '</a>',
                'transaction_date' => $transaction->transaction_date ? \Carbon\Carbon::parse($transaction->transaction_date)->format('d/m/Y') : '-',
                'user_name' => $transaction->user ? $transaction->user->present()->fullName() . ($transaction->user->employee_num ? ' (' . $transaction->user->employee_num . ')' : '') : '-',
                'items_count' => '<span class="badge badge-info">' . $transaction->items->count() . '</span>',
                'created_by_name' => $transaction->createdBy ? $transaction->createdBy->present()->fullName() : '-',
                'created_at' => $transaction->created_at ? $transaction->created_at->format('d/m/Y H:i') : '-',
                'actions' => 
                    '<a href="' . route('inventory.bulk-checkout.show', $transaction->id) . '" class="btn btn-sm btn-primary" title="Detail"><i class="fas fa-eye"></i></a> ' .
                    '<a href="' . route('inventory.bulk-checkout.edit', $transaction->id) . '" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a> ' .
                    '<a href="' . route('tool-transactions.print.accountability.form', $transaction->id) . '" class="btn btn-sm btn-success" title="Print IT Asset Accountability Form" target="_blank"><i class="fas fa-print"></i></a>'
            ];
        }

        return response()->json([
            'rows' => $rows,
            'total' => count($rows)
        ]);
    }

    /**
     * Show the form for creating a new checkout transaction.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request): View
    {
        $this->authorize('checkout', Asset::class);

        $users = User::select(['id', 'first_name', 'last_name', 'username', 'employee_num'])
            ->where('show_in_list', '=', 1)
            ->orderBy('last_name', 'asc')
            ->orderBy('first_name', 'asc')
            ->get();
        $locations = Location::orderBy('name')->get();

        $documentNumber = ToolTransaction::generateDocumentNumber('checkout');
        $checkOutStatus = $this->checkOutStatus();
        $eventChecklists = $checkOutStatus
            ? EventChecklist::where('to_status_id', $checkOutStatus->id)->whereNull('category_id')->get()
            : collect();

        // Handle single asset from redirect
        $preSelectedAsset = null;
        if ($request->has('asset_id')) {
            $preSelectedAsset = Asset::with(['model', 'location', 'assignedTo'])
                ->find($request->asset_id);
        }

        return view('inventory.bulk-checkout.create')
            ->with('users', $users)
            ->with('locations', $locations)
            ->with('documentNumber', $documentNumber)
            ->with('preSelectedAsset', $preSelectedAsset)
            ->with('eventChecklists', $eventChecklists);
    }

    /**
     * Store a newly created checkout transaction in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('checkout', Asset::class);

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string',
            'default_location' => 'nullable|exists:locations,id',
            'on_behalf' => 'nullable|string|max:255',
            'location_detail' => 'nullable|string|max:255',
            'assets' => 'required|array|min:1',
            'assets.*.asset_tag' => 'required|string',
            'assets.*.notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $checkOutStatus = $this->checkOutStatus();
        if (!$checkOutStatus) {
            return redirect()->back()->withInput()->with('error', 'The Check Out status label is not configured.');
        }

        $checkoutAssetFields = CustomField::whereIn('name', ['Loc. Detail', 'On Behalf'])
            ->get()
            ->keyBy('name');

        $assets = collect($request->input('assets'))->map(function ($assetData) {
            return Asset::where('asset_tag', $assetData['asset_tag'])->first();
        });
        if ($assets->contains(null)) {
            return redirect()->back()->withInput()->withErrors(['assets' => 'All assets must exist before checkout.']);
        }

        $checklistService = app(EventChecklistService::class);
        $checklist = $checklistService->forStatus($assets->first(), (int) $checkOutStatus->id);
        if (!$checklist) {
            return redirect()->back()->withInput()->with('error', 'The Check Out event checklist is not configured.');
        }
        $checklistErrors = $checklistService->validate($checklist, $request->input('event_checklist_rows'), $request->file('event_checklist_files'));
        if ($checklistErrors->isNotEmpty()) {
            return redirect()->back()->withInput()->withErrors($checklistErrors);
        }

        DB::beginTransaction();
        try {
            // Create the transaction
            $transaction = new ToolTransaction();
            $transaction->transaction_type = 'checkout';
            $transaction->document_number = ToolTransaction::generateDocumentNumber('checkout');
            $transaction->user_id = $request->input('user_id');
            $transaction->location_id = $request->input('default_location');
            $transaction->on_behalf = $request->input('on_behalf');
            $transaction->location_detail = $request->input('location_detail');
            $transaction->transaction_date = $request->input('transaction_date');
            $transaction->notes = $request->input('notes');
            $transaction->created_by = Auth::id();
            $transaction->save();

            // Process each asset
            $processedAssets = 0;
            $errors = [];
            $user = User::find($request->input('user_id'));

            foreach ($request->input('assets') as $index => $assetData) {
                $asset = $assets[$index];
                
                if (!$asset) {
                    $errors[] = "Asset dengan tag '{$assetData['asset_tag']}' tidak ditemukan";
                    continue;
                }

                // Check if asset is available for checkout
                if ($asset->availableForCheckout()) {
                    // Update location if default location is specified (before checkout)
                    if ($request->input('default_location')) {
                        $asset->location_id = $request->input('default_location');
                    }
                    foreach ([
                        'Loc. Detail' => 'location_detail',
                        'On Behalf' => 'on_behalf',
                    ] as $fieldName => $inputName) {
                        $field = $checkoutAssetFields->get($fieldName);
                        if ($field && $request->filled($inputName)) {
                            $asset->{$field->db_column_name()} = $request->input($inputName);
                        }
                    }
                    if ($request->filled('notes')) {
                        $asset->notes = $request->input('notes');
                    }
                    
                    // Check out the asset
                    $asset->assigned_to = $user->id;
                    $asset->assigned_type = User::class;
                    $asset->last_checkout = \Carbon\Carbon::parse($transaction->transaction_date)->startOfDay();
                    if (!$asset->save()) {
                        throw new \RuntimeException(json_encode($asset->getErrors()->toArray()));
                    }

                    $submission = $checklistService->record(
                        $checklist,
                        $asset,
                        (int) $asset->getRawOriginal('status_id'),
                        (int) $checkOutStatus->id,
                        $request->input('event_checklist_rows'),
                        Auth::id(),
                        $request->file('event_checklist_files')
                    );

                    // Create transaction item
                    $item = new ToolTransactionItem();
                    $item->transaction_id = $transaction->id;
                    $item->asset_id = $asset->id;
                    $item->event_checklist_submission_id = $submission->id;
                    $item->notes = $assetData['notes'] ?? null;
                    $item->save();

                    // Log the action
                    $asset->logCheckout($assetData['notes'] ?? null, $user);
                    
                    $processedAssets++;
                } else {
                    $errors[] = "Asset '{$assetData['asset_tag']}' tidak tersedia untuk checkout";
                }
            }

            if ($processedAssets > 0) {
                DB::commit();
                
                $message = "Berhasil check-out {$processedAssets} aset dengan nomor dokumen: {$transaction->document_number}";
                if (!empty($errors)) {
                    $message .= "\n\nPeringatan:\n" . implode("\n", $errors);
                }
                
                return redirect()->route('inventory.bulk-checkout.show', $transaction->id)
                    ->with('success', $message);
            } else {
                DB::rollback();
                return redirect()->back()
                    ->withErrors(['assets' => 'Tidak ada aset yang berhasil di-check-out. ' . implode(', ', $errors)])
                    ->withInput();
            }

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Bulk checkout error: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan saat memproses transaksi: ' . $e->getMessage()])
                ->withInput();
        }
    }

    private function checkOutStatus(): ?Statuslabel
    {
        return Statuslabel::query()
            ->where(function ($query) {
                $query->whereRaw('LOWER(name) = ?', ['check out'])
                    ->orWhereRaw('LOWER(name) = ?', ['checkout'])
                    ->orWhereRaw('LOWER(name) = ?', ['checked out']);
            })
            ->first();
    }

    /**
     * Display the specified checkout transaction.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): View
    {
        $this->authorize('view', Asset::class);

        $transaction = ToolTransaction::checkout()
            ->with(['user', 'createdBy', 'location', 'items.asset.model', 'items.checklistSubmission'])
            ->findOrFail($id);

        return view('inventory.bulk-checkout.show')
            ->with('transaction', $transaction);
    }

    /**
     * Print the specified checkout transaction.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function print($id): View
    {
        $this->authorize('view', Asset::class);

        $transaction = ToolTransaction::checkout()
            ->with(['user', 'createdBy', 'items.asset.model'])
            ->findOrFail($id);

        return view('inventory.print-transaction-accountability-form')
            ->with('transaction', $transaction);
    }

    /**
     * Show the form for editing the specified checkout transaction.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id): View
    {
        $this->authorize('update', Asset::class);

        $transaction = ToolTransaction::checkout()
            ->with(['user', 'createdBy', 'location', 'items.asset.model', 'items.checklistSubmission.checklist'])
            ->findOrFail($id);

        $users = User::select(['id', 'first_name', 'last_name', 'employee_num'])
            ->orderBy('last_name', 'asc')
            ->orderBy('first_name', 'asc')
            ->get();

        $locations = Location::orderBy('name', 'asc')->get();
        $checklistItem = $transaction->items->first(fn ($item) => $item->checklistSubmission !== null);
        $checklistSubmission = $checklistItem?->checklistSubmission;
        $checklistService = app(EventChecklistService::class);
        $checklist = $checklistSubmission?->checklist;
        $firstItem = $transaction->items->first();
        $checkOutStatus = $this->checkOutStatus();
        if (! $checklist && $firstItem?->asset && $checkOutStatus) {
            $checklist = $checklistService->forStatus($firstItem->asset, (int) $checkOutStatus->id);
        }
        $eventChecklists = $checklist ? collect([$checklist]) : collect();
        $eventChecklistValues = $checklistSubmission
            ? [$checklistSubmission->event_checklist_id => $checklistSubmission->rows ?? []]
            : [];

        return view('inventory.bulk-checkout.edit')
            ->with('transaction', $transaction)
            ->with('users', $users)
            ->with('locations', $locations)
            ->with('eventChecklists', $eventChecklists)
            ->with('eventChecklistSubmission', $checklistSubmission)
            ->with('eventChecklistValues', $eventChecklistValues);
    }

    /**
     * Update the specified checkout transaction.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $this->authorize('update', Asset::class);

        $transaction = ToolTransaction::checkout()->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'location_id' => 'nullable|exists:locations,id',
            'on_behalf' => 'nullable|string|max:255',
            'location_detail' => 'nullable|string|max:255',
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $transaction->load(['items.asset.model', 'items.checklistSubmission.checklist']);
        $checklistItem = $transaction->items->first(fn ($item) => $item->checklistSubmission !== null);
        $checklistSubmission = $checklistItem?->checklistSubmission;
        $checklist = $checklistSubmission?->checklist;
        $checklistService = app(EventChecklistService::class);
        $firstItem = $transaction->items->first();
        $checkOutStatus = $this->checkOutStatus();
        if (! $checklist && $firstItem?->asset && $checkOutStatus) {
            $checklist = $checklistService->forStatus($firstItem->asset, (int) $checkOutStatus->id);
        }

        if ($checklist) {
            $checklistErrors = $checklistService->validate(
                $checklist,
                $request->input('event_checklist_rows'),
                $request->file('event_checklist_files')
            );
            if ($checklistErrors->isNotEmpty()) {
                return redirect()->back()->withErrors($checklistErrors)->withInput();
            }
        }

        try {
            DB::beginTransaction();

            $transaction->update([
                'user_id' => $request->user_id,
                'location_id' => $request->location_id,
                'on_behalf' => $request->on_behalf,
                'location_detail' => $request->location_detail,
                'transaction_date' => $request->transaction_date,
                'notes' => $request->notes,
            ]);

            $checkoutDate = \Carbon\Carbon::parse($transaction->transaction_date)->startOfDay();
            foreach ($transaction->items as $item) {
                if (! $item->asset) {
                    continue;
                }

                $item->asset->last_checkout = $checkoutDate;
                if (! $item->asset->save()) {
                    throw new \RuntimeException(json_encode($item->asset->getErrors()->toArray()));
                }
            }

            if ($checklist && $checklistService) {
                foreach ($transaction->items as $item) {
                    if (! $item->asset) {
                        continue;
                    }

                    if ($item->checklistSubmission) {
                        $submission = $checklistService->updateSubmission(
                            $item->checklistSubmission,
                            $request->input('event_checklist_rows'),
                            $request->file('event_checklist_files')
                        );
                    } else {
                        $submission = $checklistService->record(
                            $checklist,
                            $item->asset,
                            null,
                            (int) $checklist->to_status_id,
                            $request->input('event_checklist_rows'),
                            Auth::id(),
                            $request->file('event_checklist_files')
                        );
                        $item->event_checklist_submission_id = $submission->id;
                        if (! $item->save()) {
                            throw new \RuntimeException(json_encode($item->getErrors()->toArray()));
                        }
                    }
                }
            }

            DB::commit();

            return redirect()->route('inventory.bulk-checkout.show', $transaction->id)
                ->with('success', 'Transaksi berhasil diupdate.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating bulk checkout transaction: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengupdate transaksi.')
                ->withInput();
        }
    }
}
