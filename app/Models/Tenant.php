<?php
namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Encore\Admin\Auth\Database\HasPermissions;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

/**
 * App\Models\Tenant
 *
 * @property int $id
 * @property string|null $avatar
 * @property string $nickname
 * @property string $phone_number
 * @property string|null $password
 * @property int $company_id
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Encore\Admin\Auth\Database\Role> $roles
 * @property-read int|null $roles_count
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant query()
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Tenant extends Model implements AuthenticatableContract
{
    protected $table = 'tenant';
    use Authenticatable;
    use HasPermissions;

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
