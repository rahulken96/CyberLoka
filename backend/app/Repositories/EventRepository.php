<?php

namespace App\Repositories;

use App\Interfaces\EventRepositoryInterface;
use App\Models\Event;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class EventRepository implements EventRepositoryInterface
{
    public function getAll(?string $search, ?int $limit, bool $exec)
    {
        $query = Event::where(function ($q) use ($search) {
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
            return Event::find($string);
        }

        return Event::firstWhere('event_code', $string);
    }

    public function create(array $data)
    {
        $exists = Event::where('name', $data['name'])
            ->whereDate('date_event', $data['date_event'])
            ->lockForUpdate()
            ->exists();
        if ($exists) {
            throw new Exception('Acara yang sama sudah ditambahkan di tanggal yang sama sebelumnya!', 409);
        }
        try {
            $event = new Event();
            $event->name        = $data['name'];
            $event->price       = $data['price'];
            $event->date_event  = $data['date_event'];
            $event->is_active   = $data['is_active'] ?? true;
            if (!empty($data['description'])) {
                $event->description = $data['description'];
            }
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                $event->image = $data['image']->store('assets/events', 'public');
            }
            $event->save();
            return $event;
        } catch (\Throwable $err) {
            if (isset($event->image) && Storage::disk('public')->exists($event->image)) {
                Storage::disk('public')->delete($event->image);
            }
            throw $err;
        }
    }

    public function update(string $string, array $data, bool $isWithID = false)
    {
        $event = $this->getOneData($string, $isWithID);
        if (!$event) {
            throw new Exception('Data Acara tidak ditemukan!', 404);
        }
        $newImage         = null;
        $oldImageToDelete = null;
        try {
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                $newImage = $data['image']->store('assets/events', 'public');
                $oldImageToDelete = $event->image;
                $event->image = $newImage;
            }
            if (isset($data['name'])) $event->name = $data['name'];
            if (isset($data['description'])) $event->description = $data['description'];
            if (isset($data['price'])) $event->price = $data['price'];
            if (isset($data['date_event'])) $event->date_event = $data['date_event'];
            if (isset($data['is_active'])) $event->is_active = $data['is_active'];

            $event->save();
            if ($oldImageToDelete && Storage::disk('public')->exists($oldImageToDelete)) {
                Storage::disk('public')->delete($oldImageToDelete);
            }
            return $event;
        } catch (\Throwable $err) {
            if ($newImage && Storage::disk('public')->exists($newImage)) {
                Storage::disk('public')->delete($newImage);
            }
            throw $err;
        }
    }

    public function delete(string $string, bool $isWithID = false)
    {
        $event = $this->getOneData($string, $isWithID);
        if (!$event) {
            throw new Exception('Data Acara tidak ditemukan!', 404);
        }
        // Delete image if exists
        // if ($event->image && Storage::disk('public')->exists($event->image)) {
        //     Storage::disk('public')->delete($event->image);
        // }
        $event->delete();
        return $event;
    }
}
