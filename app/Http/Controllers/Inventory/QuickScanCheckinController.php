<?php

namespace App\Http\Controllers\Inventory;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\EventChecklist;
use App\Models\EventChecklistSubmission;
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

class QuickScanCheckinController extends Controller
{
    /**
     * Display a listing of checkin transactions.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): View
    {
        $this->authorize('viewAny', Asset::class);

        return view('inventory.quickscan-checkin.index');
    }

    /**
     * Get data for DataTables
     */
    public function data(Request $request)
    {
        $this->authorize('viewAny', Asset::class);

        $query = ToolTransaction::checkin()
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
                'document_number' => '<a href="' . route('inventory.quickscan-checkin.show', $transaction->id) . '">' . $transaction->document_number . '</a>',
                'transaction_date' => $transaction->transaction_date ? \Carbon\Carbon::parse($transaction->transaction_date)->format('d/m/Y') : '-',
                'user_name' => $transaction->user ? $transaction->user->present()->fullName() . ($transaction->user->employee_num ? ' (' . $transaction->user->employee_num . ')' : '') : '-',
                'items_count' => '<span class="badge badge-info">' . $transaction->items->count() . '</span>',
                'created_by_name' => $transaction->createdBy ? $transaction->createdBy->present()->fullName() : '-',
                'created_at' => $transaction->created_at ? $transaction->created_at->format('d/m/Y H:i') : '-',
                'actions' => 
                    '<a href="' . route('inventory.quickscan-checkin.show', $transaction->id) . '" class="btn btn-sm btn-primary" title="Detail"><i class="fas fa-eye"></i></a> ' .
                    '<a href="' . route('inventory.quickscan-checkin.edit', $transaction->id) . '" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a> ' .
                    '<a href="' . route('tool-transactions.print.return.form', $transaction->id) . '" class="btn btn-sm btn-success" title="Print IT Asset Return Form" target="_blank"><i class="fas fa-print"></i></a>'
            ];
        }

        return response()->json([
            'rows' => $rows,
            'total' => count($rows)
        ]);
    }

    /**
     * Show the form for creating a new checkin transaction.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request): View
    {
        $this->authorize('create', Asset::class);

        $users = User::select(['id', 'first_name', 'last_name', 'username', 'employee_num'])
            ->where('show_in_list', '=', 1)
            ->orderBy('last_name', 'asc')
            ->orderBy('first_name', 'asc')
            ->get();

        $documentNumber = ToolTransaction::generateDocumentNumber('checkin');
        $inCheckingStatus = $this->inCheckingStatus();
        $eventChecklists = $inCheckingStatus
            ? EventChecklist::where('to_status_id', $inCheckingStatus->id)->whereNull('category_id')->get()
            : collect();

        // Handle single asset from redirect
        $preSelectedAsset = null;
        if ($request->has('asset_id')) {
            $preSelectedAsset = Asset::with(['model', 'location'])
                ->find($request->asset_id);
        }

        return view('inventory.quickscan-checkin.create')
            ->with('users', $users)
            ->with('documentNumber', $documentNumber)
            ->with('preSelectedAsset', $preSelectedAsset)
            ->with('eventChecklists', $eventChecklists);
    }

    /**
     * Store a newly created checkin transaction in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Asset::class);

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string',
            'default_location' => 'nullable|exists:locations,id',
            'assets' => 'required|array|min:1',
            'assets.*.asset_tag' => 'required|string',
            'assets.*.notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $inCheckingStatus = $this->inCheckingStatus();
        if (!$inCheckingStatus) {
            return redirect()->back()->withInput()->with('error', 'The In-Checking status label is not configured.');
        }

        $assets = collect($request->input('assets'))->map(function ($assetData) {
            return Asset::where('asset_tag', $assetData['asset_tag'])->first();
        });
        if ($assets->contains(null)) {
            return redirect()->back()->withInput()->withErrors(['assets' => 'All assets must exist before check-in.']);
        }

        $checklistService = app(EventChecklistService::class);
        $checklist = $checklistService->forStatus($assets->first(), (int) $inCheckingStatus->id);
        if (!$checklist) {
            return redirect()->back()->withInput()->with('error', 'The In-Checking event checklist is not configured.');
        }
        $checklistErrors = $checklistService->validate($checklist, $request->input('event_checklist_rows'), $request->file('event_checklist_files'));
        if ($checklistErrors->isNotEmpty()) {
            return redirect()->back()->withInput()->withErrors($checklistErrors);
        }

        DB::beginTransaction();
        try {
            // Create the transaction
            $transaction = new ToolTransaction();
            $transaction->transaction_type = 'checkin';
            $transaction->document_number = ToolTransaction::generateDocumentNumber('checkin');
            $transaction->user_id = $request->input('user_id');
            $transaction->transaction_date = $request->input('transaction_date');
            $transaction->notes = $request->input('notes');
            $transaction->created_by = Auth::id();
            $transaction->save();

            // Process each asset
            $processedAssets = 0;
            $errors = [];

            foreach ($request->input('assets') as $index => $assetData) {
                $asset = $assets[$index];
                
                if (!$asset) {
                    $errors[] = "Asset dengan tag '{$assetData['asset_tag']}' tidak ditemukan";
                    continue;
                }

                // Check if asset is currently checked out
                if ($asset->assigned_to) {
                    // Check in the asset
                    $asset->assigned_to = null;
                    $asset->assigned_type = null;
                    $asset->last_checkin = \Carbon\Carbon::parse($transaction->transaction_date)->startOfDay();
                    $oldStatusId = (int) $asset->status_id;
                    $asset->status_id = $inCheckingStatus->id;
                    
                    // Update location if default location is specified
                    if ($request->input('default_location')) {
                        $asset->location_id = $request->input('default_location');
                    }
                    
                    if (!$asset->save()) {
                        throw new \RuntimeException(json_encode($asset->getErrors()->toArray()));
                    }

                    $submission = $checklistService->record(
                        $checklist,
                        $asset,
                        $oldStatusId,
                        (int) $inCheckingStatus->id,
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
                    $asset->logCheckin(User::find($request->input('user_id')), $assetData['notes'] ?? null);
                    
                    $processedAssets++;
                } else {
                    $errors[] = "Asset '{$assetData['asset_tag']}' sudah dalam status checked in";
                }
            }

            if ($processedAssets > 0) {
                DB::commit();
                
                $message = "Berhasil check-in {$processedAssets} aset dengan nomor dokumen: {$transaction->document_number}";
                if (!empty($errors)) {
                    $message .= "\n\nPeringatan:\n" . implode("\n", $errors);
                }
                
                return redirect()->route('inventory.quickscan-checkin.show', $transaction->id)
                    ->with('success', $message);
            } else {
                DB::rollback();
                return redirect()->back()
                    ->withErrors(['assets' => 'Tidak ada aset yang berhasil di-check-in. ' . implode(', ', $errors)])
                    ->withInput();
            }

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Quick scan checkin error: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan saat memproses transaksi: ' . $e->getMessage()])
                ->withInput();
        }
    }

    private function inCheckingStatus(): ?Statuslabel
    {
        return Statuslabel::query()
            ->where(function ($query) {
                $query->whereRaw('LOWER(name) = ?', ['in checking'])
                    ->orWhereRaw('LOWER(name) = ?', ['in-checking'])
                    ->orWhereRaw('LOWER(name) = ?', ['in_checking']);
            })
            ->first();
    }

    /**
     * Display the specified checkin transaction.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): View
    {
        $this->authorize('view', Asset::class);

        $transaction = ToolTransaction::checkin()
            ->with(['user', 'createdBy', 'location', 'items.asset.model', 'items.checklistSubmission.checklist'])
            ->findOrFail($id);
        $this->checklistEditData($transaction);

        return view('inventory.quickscan-checkin.show')
            ->with('transaction', $transaction);
    }

    /**
     * Print the specified checkin transaction.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function print($id): View
    {
        $this->authorize('view', Asset::class);

        $transaction = ToolTransaction::checkin()
            ->with(['user', 'createdBy', 'items.asset.model'])
            ->findOrFail($id);

        return view('inventory.print-transaction-return-form')
            ->with('transaction', $transaction);
    }

    /**
     * Show the form for editing the specified checkin transaction.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id): View
    {
        $this->authorize('update', Asset::class);

        $transaction = ToolTransaction::checkin()
            ->with(['user', 'createdBy', 'location', 'items.asset.model', 'items.checklistSubmission.checklist'])
            ->findOrFail($id);

        $users = User::select(['id', 'first_name', 'last_name', 'employee_num'])
            ->orderBy('last_name', 'asc')
            ->orderBy('first_name', 'asc')
            ->get();

        $locations = Location::orderBy('name', 'asc')->get();
        [$eventChecklists, $checklistSubmission, $eventChecklistValues] = $this->checklistEditData($transaction);

        return view('inventory.quickscan-checkin.edit')
            ->with('transaction', $transaction)
            ->with('users', $users)
            ->with('locations', $locations)
            ->with('eventChecklists', $eventChecklists)
            ->with('eventChecklistSubmission', $checklistSubmission)
            ->with('eventChecklistValues', $eventChecklistValues);
    }

    /**
     * Update the specified checkin transaction.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $this->authorize('update', Asset::class);

        $transaction = ToolTransaction::checkin()->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'location_id' => 'nullable|exists:locations,id',
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $transaction->load(['items.asset.model', 'items.checklistSubmission.checklist']);
        [$eventChecklists, $checklistSubmission] = $this->checklistEditData($transaction);
        $checklist = $eventChecklists->first();
        $checklistService = app(EventChecklistService::class);
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
                'transaction_date' => $request->transaction_date,
                'notes' => $request->notes,
            ]);

            $checkinDate = \Carbon\Carbon::parse($transaction->transaction_date)->startOfDay();
            foreach ($transaction->items as $item) {
                if (! $item->asset) {
                    continue;
                }

                $item->asset->last_checkin = $checkinDate;
                if (! $item->asset->save()) {
                    throw new \RuntimeException(json_encode($item->asset->getErrors()->toArray()));
                }
            }

            if ($checklist) {
                foreach ($transaction->items as $item) {
                    if (! $item->asset) {
                        continue;
                    }

                    $itemSubmission = $item->checklistSubmission;
                    if ($itemSubmission) {
                        $checklistService->updateSubmission(
                            $itemSubmission,
                            $request->input('event_checklist_rows'),
                            $request->file('event_checklist_files')
                        );
                        if ((int) $item->event_checklist_submission_id !== (int) $itemSubmission->id) {
                            $item->event_checklist_submission_id = $itemSubmission->id;
                            if (! $item->save()) {
                                throw new \RuntimeException(json_encode($item->getErrors()->toArray()));
                            }
                        }
                    } else {
                        $itemSubmission = $checklistService->record(
                            $checklist,
                            $item->asset,
                            null,
                            (int) $checklist->to_status_id,
                            $request->input('event_checklist_rows'),
                            Auth::id(),
                            $request->file('event_checklist_files')
                        );
                        $item->event_checklist_submission_id = $itemSubmission->id;
                        if (! $item->save()) {
                            throw new \RuntimeException(json_encode($item->getErrors()->toArray()));
                        }
                    }
                }
            }

            DB::commit();

            return redirect()->route('inventory.quickscan-checkin.show', $transaction->id)
                ->with('success', 'Transaksi berhasil diupdate.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating quickscan checkin transaction: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengupdate transaksi.')
                ->withInput();
        }
    }

    private function checklistEditData(ToolTransaction $transaction): array
    {
        $checklistItem = $transaction->items->first(fn ($item) => $item->checklistSubmission !== null);
        $checklistSubmission = $checklistItem?->checklistSubmission;
        $checklistService = app(EventChecklistService::class);
        $checklist = $checklistSubmission?->checklist;
        $firstItem = $transaction->items->first();
        $inCheckingStatus = $this->inCheckingStatus();

        if (! $checklistSubmission && $inCheckingStatus && $transaction->created_at) {
            foreach ($transaction->items as $item) {
                if (! $item->asset) {
                    continue;
                }

                $submission = EventChecklistSubmission::query()
                    ->with('checklist')
                    ->where('asset_id', $item->asset_id)
                    ->where('to_status_id', $inCheckingStatus->id)
                    ->whereBetween('created_at', [
                        $transaction->created_at,
                        $transaction->created_at->copy()->addMinute(),
                    ])
                    ->orderBy('created_at')
                    ->first();
                if ($submission) {
                    $item->setRelation('checklistSubmission', $submission);
                    $checklistItem = $item;
                    $checklistSubmission = $submission;
                    $checklist = $submission->checklist;
                    break;
                }
            }
        }

        if (! $checklist && $firstItem?->asset && $inCheckingStatus) {
            $checklist = $checklistService->forStatus($firstItem->asset, (int) $inCheckingStatus->id);
        }

        return [
            $checklist ? collect([$checklist]) : collect(),
            $checklistSubmission,
            $checklistSubmission
                ? [$checklistSubmission->event_checklist_id => $checklistSubmission->rows ?? []]
                : [],
        ];
    }
}
