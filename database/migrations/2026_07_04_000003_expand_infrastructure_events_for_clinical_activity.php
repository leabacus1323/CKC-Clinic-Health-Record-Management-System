<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('infrastructure_events', function (Blueprint $table) {
            $table->unsignedTinyInteger('actor_user_type')->nullable()->after('actor_user_id')->index();
            $table->string('actor_role', 80)->nullable()->after('actor_user_type');
            $table->string('event_category', 50)->default('application')->after('action')->index();
            $table->string('description', 255)->nullable()->after('event_category');
            $table->string('user_agent', 255)->nullable()->after('ip_address');
        });

        DB::table('infrastructure_events')
            ->whereNotNull('actor_user_id')
            ->orderBy('id')
            ->chunkById(200, function ($events): void {
                $users = DB::table('users')
                    ->whereIn('id', $events->pluck('actor_user_id')->filter()->unique())
                    ->pluck('user_type', 'id');

                foreach ($events as $event) {
                    $type = isset($users[$event->actor_user_id]) ? (int) $users[$event->actor_user_id] : null;
                    DB::table('infrastructure_events')->where('id', $event->id)->update([
                        'actor_user_type' => $type,
                        'actor_role' => $this->roleLabel($type),
                    ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('infrastructure_events', function (Blueprint $table) {
            $table->dropIndex(['actor_user_type']);
            $table->dropIndex(['event_category']);
            $table->dropColumn([
                'actor_user_type',
                'actor_role',
                'event_category',
                'description',
                'user_agent',
            ]);
        });
    }

    private function roleLabel(?int $type): ?string
    {
        return match ($type) {
            0 => 'System Administrator',
            2 => 'School Nurse / Clinic Staff',
            3 => 'School Doctor',
            default => null,
        };
    }
};
