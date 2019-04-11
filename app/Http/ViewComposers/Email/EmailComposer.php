<?php

namespace App\Http\ViewComposers\Email;

use App\Models\Block;
use App\Models\Setting;
use Illuminate\Contracts\View\View;

class EmailComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $email_footer_block = Block::findOrFail(1);
        $base_code = Setting::findOrFail(1);

        $view->with('email_footer_block', $email_footer_block)->with('base_code', $base_code);
    }
}
