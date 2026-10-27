<?php

namespace App\Services;

use App\Models\Business;
use App\Models\BusinessJsonImport;
use App\Models\BusinessReview;
use App\Models\BusinessVerification;
use App\Models\CommunityContribution;
use App\Models\VerificationRequest;
use Illuminate\Support\Facades\DB;

class BusinessJsonImportCleanup
{
    public function deleteImport(BusinessJsonImport $import): int
    {
        return DB::transaction(function () use ($import): int {
            $businessIds = Business::query()
                ->where('business_json_import_id', $import->id)
                ->pluck('id');

            if ($businessIds->isEmpty()) {
                $import->delete();

                return 0;
            }

            BusinessReview::query()->whereIn('business_id', $businessIds)->delete();
            VerificationRequest::query()->whereIn('business_id', $businessIds)->delete();
            CommunityContribution::query()->whereIn('business_id', $businessIds)->delete();
            BusinessVerification::query()->whereIn('business_id', $businessIds)->delete();

            $deleted = Business::query()
                ->whereIn('id', $businessIds)
                ->delete();

            $import->delete();

            return $deleted;
        });
    }
}
