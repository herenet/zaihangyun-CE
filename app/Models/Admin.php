<?php
namespace App\Models;

use Illuminate\Support\Collection;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Encore\Admin\Auth\Database\Permission;
use Encore\Admin\Auth\Database\HasPermissions;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

/**
 * App\Models\Admin
 *
 * @property int $id
 * @property string|null $avatar
 * @property string $nickname
 * @property string $username
 * @property string|null $password
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon $created_at
 * @mixin \Eloquent
 */
class Admin extends Model implements AuthenticatableContract
{
    protected $table = 'admin';
    use Authenticatable;
    use HasPermissions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nickname',
        'username',
        'password',
        'avatar',
    ];

     /**
     * A user has and belongs to many roles.
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        $pivotTable = config('admin.database.role_users_table');

        $relatedModel = config('admin.database.roles_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'user_id', 'role_id');
    }

    public function allPermissions(): Collection
    {
        return collect([new Permission(['id' => '*', 'name' => '*', 'slug' => '*'])]);
    }

    public function getAvatarAttribute($avatar)
    {
        if ($avatar && filter_var($avatar, FILTER_VALIDATE_URL)) {
            return $avatar;
        }

        $disk = config('admin.upload.disk');

        if ($avatar && array_key_exists($disk, config('filesystems.disks'))) {
            return Storage::disk(config('admin.upload.disk'))->url($avatar);
        }

        $default = config('admin.default_avatar') ?: '/vendor/laravel-admin/AdminLTE/dist/img/user2-160x160.jpg';

        return admin_asset($default);
    }

    public function getNameAttribute()
    {
        return $this->nickname;
    }
}
