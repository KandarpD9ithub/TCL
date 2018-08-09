<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'mobile', 'mobile_id', 'role_name', 'has_wallet_permission'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee()
    {
        return $this->hasOne('App\Employee');
    }

    /**
     * @param string $route
     * @return bool
     */
    public function hasPermission($route)
    {
        $userRole = $this->getUserRole();
        $roleBasedRestrictedRoutes = $this->roleBasedRestrictedRoutes();
        return (null != $roleBasedRestrictedRoutes[$userRole] && !empty($roleBasedRestrictedRoutes[$userRole])
            && in_array($route, $roleBasedRestrictedRoutes[$userRole])

        ) ? false : true;
    }

    /**
     * @return array
     */
    private function roleBasedRestrictedRoutes()
    {
        $roles = array_keys(\Config::get('constants.ROLE_NAME'));

        return array_combine($roles, [
            ['change-password', 'change-password.store','product-price.index', 'product-price.create',
                'product-price.store', 'menu.index', 'special-product.index', 'special-product.create',
                'special-product.store', 'special-product.edit', 'special-product.update', 'special-product.destroy',
                'product-price.edit', 'product-price.update', 'home','taxes.index'],
            ['change-password', 'change-password.store','product-price.index', 'product-price.create',
                'product-price.store', 'menu.index', 'special-product.index', 'special-product.create',
                'special-product.store', 'special-product.edit', 'special-product.update', 'special-product.destroy',
                'product-price.edit', 'product-price.update', 'home','taxes.index'],
            ['change-password', 'change-password.store','product-price.index', 'product-price.create',
                'product-price.store', 'menu.index', 'menu.inactiveMenuItems', 'product-price.edit',
                'product-price.update', 'special-product.index', 'special-product.create', 'special-product.store',
                'special-product.edit', 'special-product.update', 'special-product.destroy', 'home',
                'order.track-time', 'category.sale', 'item.sale', 'sale.report', 'total.sale', 'totalSaleExcel',
                'saleReportExcel', 'trackOrderTimeExcel', 'itemSaleExcel', 'categoryWiseExcel','taxes.index','taxes.create'],
            [],
        ]);
    }

    private function getUserRole()
    {
        return \Auth::user()->role_name;
    }
}
