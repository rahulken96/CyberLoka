<?php

namespace App\Repositories;

use App\Helpers\StringHelper;
use App\Interfaces\SocialAssistanceRecipientRepositoryInterface;
use App\Interfaces\SocialAssistanceRepositoryInterface;
use App\Models\SocialAssistance;
use App\Models\SocialAssistanceRecipient;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SocialAssistanceRecipientRepository implements SocialAssistanceRecipientRepositoryInterface
{
    public function getAll(?string $search, ?int $limit, bool $exec)
    {
        $query = SocialAssistanceRecipient::with(['socialAssistance', 'headOfFamily'])
            ->where(function($q) use ($search) {
                if ($search) {
                    $q->search($search);
                }
            });

        $query->orderByDesc('created_at');

        if ($limit) {
            $query->take($limit);
        }

        if ($exec) {
            return $query->get();
        }

        return $query;
    }

    public function getAllPaginate(?string $search, ?int $rowsPerPage)
    {
        $query = $this->getAll($search, $rowsPerPage, false);
        return $query->paginate($rowsPerPage);
    }
    
    public function getOneData(string $string, bool $isWithID = false)
    {
        if ($isWithID) {
            if (!ctype_digit($string)) {
                return null;
            }
            return SocialAssistanceRecipient::with(['socialAssistance', 'headOfFamily'])
                ->find($string);
        }

        return SocialAssistanceRecipient::with(['socialAssistance', 'headOfFamily'])
            ->firstWhere('social_recipient_code', $string);
    }
}
