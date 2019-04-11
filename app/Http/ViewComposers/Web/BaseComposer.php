<?php

namespace App\Http\ViewComposers\Web;

use App\Models\Setting;
use Illuminate\Contracts\View\View;

class BaseComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $base_code = Setting::findOrFail(1);

        $view->with('base_code', $base_code);
    }
}
