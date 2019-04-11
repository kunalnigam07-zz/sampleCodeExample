<?php

namespace App\Http\ViewComposers\Admin;

use AuthHelper;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use App\Models\Permission;

class AdminPermissionComposer
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function renderSub($child, $perms)
    {
        if ($child->status == 1 && AuthHelper::canShowWLMenu($child->permission)) {
            $item = '<input name="permissions[]" type="checkbox" data-label="&nbsp;&nbsp;&nbsp;&mdash;&nbsp;&nbsp;&nbsp;' . $child->title . '" value="' . $child->id . '_' . $child->parent_id . '" class="kid-' . $child->parent_id . '"' . (in_array($child->id, $perms) ? ' checked="checked"' : '') . '><br>';
        } else {
            $item = '';
        }

        return $item;
    }

    public function renderMenu($node, $perms)
    {
        $item = '';

        $item .= '<input name="permissions[]" type="checkbox" data-label="' . $node->title . '" value="' . $node->id . '_0" class="fakecheck"' . (in_array($node->id, $perms) ? ' checked="checked"' : '') . '><br>';

        if ($node->children()->count() > 0) {
            foreach ($node->children as $child) {
                $item .= $this->renderSub($child, $perms);
            }
        }

        return $item;
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $entry = $view->getData();
        $perms = [];
        $admin_permissions = '<fieldset' . ($entry['entry']->admin_type == 1 ? ' style="display:none;"' : '') . ' id="perm_zone"><label>&nbsp;</label><div class="field">';

        foreach ($entry['entry']->permissions as $pm) {
            $perms[] = $pm->id;
        }

        $nodes = Permission::roots()->where('is_menu', 1)->get();
        foreach ($nodes as $node) {
            $admin_permissions .= $this->renderMenu($node, $perms);
        }

         $admin_permissions .= '</div></fieldset>';

        $view->with('admin_permissions', $admin_permissions);
    }
}
