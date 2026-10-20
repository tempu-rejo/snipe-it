<?php

namespace Database\Seeders;

use App\Models\EventChecklist;
use App\Models\EventChecklistTemplate;
use App\Models\Statuslabel;
use Illuminate\Database\Seeder;

class EventChecklistSeeder extends Seeder
{
    public function run(): void
    {
        $columns = [
            [
                'key' => 'no',
                'label' => 'No.',
                'type' => 'number',
                'required' => false
            ],
            [
                'key' => 'area',
                'label' => 'Area',
                'type' => 'text',
                'required' => false
            ],
            [
                'key' => 'checkpoint',
                'label' => 'Mandatory Checkpoint',
                'type' => 'textarea',
                'required' => true
            ],
            [
                'key' => 'result',
                'label' => 'Result',
                'type' => 'select',
                'required' => true,
                'options' => [
                    'PASS',
                    'FAIL',
                    'PENDING',
                    'N/A'
                ]
            ],
            [
                'key' => 'evidence',
                'label' => 'Evidence / Reference',
                'type' => 'textarea',
                'required' => false
            ],
            [
                'key' => 'notes',
                'label' => 'Finding / Notes',
                'type' => 'textarea',
                'required' => false
            ],
            [
                'key' => 'pic_due',
                'label' => 'PIC / Due Date',
                'type' => 'text',
                'required' => false
            ]
        ];

        $templates = [
            '00 START' => [],
            '1 NEW' => [
                [
                    'no' => 1,
                    'area' => 'Document',
                    'checkpoint' => 'Approved PO, Invoices document available',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 2,
                    'area' => 'Document',
                    'checkpoint' => 'Vendor and delivery note match',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 3,
                    'area' => 'Receiving',
                    'checkpoint' => 'Quantity, brand, model, and specifications match',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 4,
                    'area' => 'Identity',
                    'checkpoint' => 'Serial number verified ',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 5,
                    'area' => 'Receiving',
                    'checkpoint' => 'Warranty information recorded',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 6,
                    'area' => 'Physical',
                    'checkpoint' => 'Packaging and asset condition acceptable',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 7,
                    'area' => 'Physical',
                    'checkpoint' => 'Charger, cable, adapter, and accessories complete',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 8,
                    'area' => 'UI-TrackIN',
                    'checkpoint' => 'Asset record created  with correct:\nAsset name, Asset Tag, Serial, Brand/Model, category, Location/Loc. Detail, Device type, Notes /  Additional Notes',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 9,
                    'area' => 'Evidence',
                    'checkpoint' => 'Asset photo and serial-number photo attached',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ]
            ],
            '2 STAGING' => [
                [
                    'no' => 1,
                    'area' => 'Identification',
                    'checkpoint' => 'Asset tag attached securely',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 2,
                    'area' => 'Identification',
                    'checkpoint' => 'QR / barcode scans and opens the correct asset',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 3,
                    'area' => 'Configuration',
                    'checkpoint' => 'Hostname follows naming standard',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 4,
                    'area' => 'Hardware',
                    'checkpoint' => 'Approved hardware specification confirmed (non PC)',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 5,
                    'area' => 'Hardware',
                    'checkpoint' => 'Storage, memory, CPU / GPU, ports, camera, audio, mic, batt, etc',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 6,
                    'area' => 'OS',
                    'checkpoint' => 'Approved OS activated and patched',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 7,
                    'area' => 'OS',
                    'checkpoint' => 'Date, time zone, and synchronization correct',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 8,
                    'area' => 'Security',
                    'checkpoint' => 'Join Domain successfully',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 9,
                    'area' => 'Security',
                    'checkpoint' => 'BitLocker enabled and recovery key stored',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 10,
                    'area' => 'Security',
                    'checkpoint' => 'Defender active and updated',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 11,
                    'area' => 'Security',
                    'checkpoint' => 'ME installed',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 12,
                    'area' => 'Security',
                    'checkpoint' => 'VPN installed',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 13,
                    'area' => 'Security',
                    'checkpoint' => 'Any local administrator access complies with policy',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 14,
                    'area' => 'Applications',
                    'checkpoint' => 'Microsoft 365 installed and activated',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 15,
                    'area' => 'Applications',
                    'checkpoint' => 'Standard company applications (Team Viewer, 7zip, Chrome) and agents installed',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 16,
                    'area' => 'Applications',
                    'checkpoint' => 'Role-based applications  match approved request (eg: Nitro Pdf+, Corel, ScetchUP, Bartender, etc) ',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 17,
                    'area' => 'Applications',
                    'checkpoint' => 'No unauthorized software found',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 18,
                    'area' => 'Evidence',
                    'checkpoint' => 'Confirmation minimum evidence attached: ME, hostname, BitLocker, and Defender ',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ]
            ],
            '3 READY TO DEPLOY' => [
                [
                    'no' => 1,
                    'area' => 'Prerequisite',
                    'checkpoint' => 'STAGING / In Checking checklist completed',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 2,
                    'area' => 'Final QC',
                    'checkpoint' => 'No critical / high security finding remains',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 3,
                    'area' => 'Final QC',
                    'checkpoint' => 'Hardware and functional checks pass',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 4,
                    'area' => 'Final QC',
                    'checkpoint' => 'Connectivity, restart, and shutdown tests pass',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 5,
                    'area' => 'Final QC',
                    'checkpoint' => 'Battery and charger acceptable where applicable',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 6,
                    'area' => 'Final QC',
                    'checkpoint' => 'Final QR scan opens the correct record',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 7,
                    'area' => 'Approval',
                    'checkpoint' => 'No FAIL item; pending item has acceptance, PIC, and target date',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 8,
                    'area' => 'Approval',
                    'checkpoint' => 'IT QA reviewer approves release',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ]
            ],
            '4 CHECK OUT' => [
                [
                    'no' => 1,
                    'area' => 'Custody',
                    'checkpoint' => 'User / PIC, location, location detail and notes correct',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 2,
                    'area' => 'Custody',
                    'checkpoint' => 'Asset tag, serial number, and hostname verified',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 3,
                    'area' => 'Custody',
                    'checkpoint' => 'Assignment date and handover condition recorded',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 4,
                    'area' => 'Custody',
                    'checkpoint' => 'Charger and accessories recorded',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 5,
                    'area' => 'Custody',
                    'checkpoint' => 'Approved deployment request available',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 6,
                    'area' => 'Custody',
                    'checkpoint' => 'Handover acknowledgment completed & photographed',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 7,
                    'area' => 'User Test',
                    'checkpoint' => 'User login and MFA successful',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 8,
                    'area' => 'User Test',
                    'checkpoint' => 'Outlook, Teams, OneDrive, VPN, and required applications tested',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 9,
                    'area' => 'Handover form',
                    'checkpoint' => 'IT Asset Accountability form submitted',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 10,
                    'area' => 'Guidance',
                    'checkpoint' => 'User received device care brief guidance',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ]
            ],
            '5 IN CHECKING' => [
                [
                    'no' => 1,
                    'area' => 'Return',
                    'checkpoint' => 'Return date and reason recorded',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 2,
                    'area' => 'Return',
                    'checkpoint' => 'Asset tag and serial number match',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 3,
                    'area' => 'Return',
                    'checkpoint' => 'Charger and accessories checked completed',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 4,
                    'area' => 'Return',
                    'checkpoint' => 'Previous user assignment removed (if separation/resign)',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 5,
                    'area' => 'Condition',
                    'checkpoint' => 'Body, casing, display, keyboard, ports, and accessories physically inspected',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 6,
                    'area' => 'Condition',
                    'checkpoint' => 'Battery, storage, memory, and connectivity functionality checked',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 7,
                    'area' => 'Condition',
                    'checkpoint' => 'Damage or missing item identified & photographed (attached)',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 8,
                    'area' => 'Data',
                    'checkpoint' => 'Business-data cleansing (if separation/resign)',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 9,
                    'area' => 'Identification',
                    'checkpoint' => 'QR / barcode scans and opens the correct asset, reprint label if necessary',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 10,
                    'area' => 'Decision',
                    'checkpoint' => 'Disposition selected: RTD,in repair, irreparable, obsolete',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ]
            ],
            '6 REPAIR IT' => [
                [
                    'no' => 1,
                    'area' => 'Diagnosis',
                    'checkpoint' => 'Issue and problem recorded',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 2,
                    'area' => 'Diagnosis',
                    'checkpoint' => 'Diagnosis / root cause recorded',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 3,
                    'area' => 'Control',
                    'checkpoint' => 'Warranty status checked',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 4,
                    'area' => 'Repair',
                    'checkpoint' => 'Repair action and replaced component recorded',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 5,
                    'area' => 'Testing',
                    'checkpoint' => 'Functional and hardware diagnostic tests pass',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 6,
                    'area' => 'Testing',
                    'checkpoint' => 'Connectivity and security configuration rechecked',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 7,
                    'area' => 'Testing',
                    'checkpoint' => 'Stability / burn-in test completed where required',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 8,
                    'area' => 'Decision',
                    'checkpoint' => 'Outcome selected: fixed, vendor escalation, or irreparable',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ]
            ],
            '7 REPAIR VENDOR' => [
                [
                    'no' => 1,
                    'area' => 'Dispatch',
                    'checkpoint' => 'Repair recommendation and vendor available',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 2,
                    'area' => 'Dispatch',
                    'checkpoint' => 'Vendor\'s repair cost estimation',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 3,
                    'area' => 'Dispatch',
                    'checkpoint' => 'Serial number and pre-dispatch condition verified',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 5,
                    'area' => 'Dispatch',
                    'checkpoint' => 'Vendor handover, courier and SJ recorded',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 6,
                    'area' => 'Return',
                    'checkpoint' => 'Asset and serial number verified on return',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 7,
                    'area' => 'Return',
                    'checkpoint' => 'Vendor repair report and replaced parts recorded',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 8,
                    'area' => 'Return',
                    'checkpoint' => 'Repair warranty and actual cost recorded',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 9,
                    'area' => 'Testing',
                    'checkpoint' => 'Physical and functional tests pass',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 10,
                    'area' => 'Decision',
                    'checkpoint' => 'Outcome selected: repaired or irreparable',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ]
            ],
            '8 IN DISPOSAL' => [
                [
                    'no' => 1,
                    'area' => 'Eligibility',
                    'checkpoint' => 'Technical assessment supports irreparable / obsolete decision',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 2,
                    'area' => 'Eligibility',
                    'checkpoint' => 'Repair-cost comparison available where relevant',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 3,
                    'area' => 'Approval',
                    'checkpoint' => 'Finance / depreciation status confirmed',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 4,
                    'area' => 'Approval',
                    'checkpoint' => 'Management and Finance approvals available',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 5,
                    'area' => 'Data',
                    'checkpoint' => 'User and assignment removed',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 6,
                    'area' => 'Data',
                    'checkpoint' => 'Required business data secured',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 7,
                    'area' => 'Data',
                    'checkpoint' => 'Storage wipe / sanitization completed with evidence',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 8,
                    'area' => 'Data',
                    'checkpoint' => 'Asset removed from management / security platform at closure',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 9,
                    'area' => 'Disposal',
                    'checkpoint' => 'Asset tag, serial number, and final condition verified',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 10,
                    'area' => 'Disposal',
                    'checkpoint' => 'Disposal method and vendor recorded',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 11,
                    'area' => 'Disposal',
                    'checkpoint' => 'Handover / collection evidence available',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 12,
                    'area' => 'Closure',
                    'checkpoint' => 'Disposal certificate received',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 13,
                    'area' => 'Closure',
                    'checkpoint' => 'Disposal date and DISPOSED status recorded',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ],
                [
                    'no' => 14,
                    'area' => 'Closure',
                    'checkpoint' => 'Asset removed from assignable inventory',
                    'evidence' => null,
                    'notes' => null,
                    'pic_due' => null
                ]
            ]
        ];

        foreach ($templates as $sheet => $rows) {
            if (empty($rows)) {
                continue;
            }

            $targetStatus = $this->findOrCreateStatus($sheet);
            $checklists = EventChecklist::withTrashed()
                ->with('template')
                ->where('name', $sheet)
                ->whereNull('category_id')
                ->whereNull('from_status_id')
                ->orderBy('id')
                ->get();
            $activeChecklists = $checklists->reject(fn (EventChecklist $checklist): bool => $checklist->trashed());
            $checklist = $activeChecklists->first() ?? $checklists->first();
            $template = $checklist?->template ?? EventChecklistTemplate::where('name', $sheet)->first();
            $template ??= new EventChecklistTemplate();
            $template->fill([
                'name' => $sheet,
                'columns' => $columns,
                'rows' => $rows,
            ])->saveOrFail();

            if ($activeChecklists->isEmpty()) {
                $checklist ??= new EventChecklist();
                if ($checklist->trashed()) {
                    $checklist->restore();
                }
                $activeChecklists = collect([$checklist]);
            }

            foreach ($activeChecklists as $eventChecklist) {
                $eventChecklist->fill([
                    'category_id' => null,
                    'from_status_id' => null,
                    'to_status_id' => $targetStatus->id,
                    'name' => $sheet,
                    'template_id' => $template->id,
                ])->saveOrFail();
            }
        }
    }

    private function findOrCreateStatus(string $sheet): Statuslabel
    {
        $name = preg_replace('/^\d+\s+/', '', trim($sheet));
        $normalized = fn (string $value): string => trim(preg_replace('/[^a-z0-9]+/', ' ', strtolower($value)));
        $wanted = $normalized($name);

        $status = Statuslabel::query()->get()->first(
            fn (Statuslabel $status): bool => $normalized($status->name) === $wanted
        );

        if ($status) {
            return $status;
        }

        $aliases = [
            'new' => 'Pending',
            'staging' => 'Staging',
            'ready to deploy' => 'Ready to Deploy',
            'check out' => 'Check Out',
            'in checking' => 'In-Checking',
            'repair it' => 'IN Repair - IT',
            'repair vendor' => 'IN Repair - Vendor',
            'in disposal' => 'In Disposal',
        ];
        $statusName = $aliases[$wanted] ?? $name;

        $status = Statuslabel::query()->get()->first(
            fn (Statuslabel $candidate): bool => $normalized($candidate->name) === $normalized($statusName)
        );

        if ($status) {
            return $status;
        }

        $status = new Statuslabel([
            'name' => $statusName,
            'deployable' => in_array($wanted, ['ready to deploy', 'check out'], true) ? 1 : 0,
            'pending' => in_array($wanted, ['new', 'staging'], true) ? 1 : 0,
            'archived' => $wanted === 'in disposal' ? 1 : 0,
        ]);
        $status->saveOrFail();

        return $status;
    }
}
