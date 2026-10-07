<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        $creator = User::where('user_type', User::TYPE_STAFF)
            ->orWhere('user_type', User::TYPE_ADMIN)
            ->first();

        Announcement::create([
            'title' => 'Welcome to CKC Clinic Health Record Management System',
            'message' => 'Announcements are now enabled. Please check the staff announcements page for the latest reminders and notices.',
            'created_by' => $creator?->id,
            'active' => true,
        ]);
    }
}
