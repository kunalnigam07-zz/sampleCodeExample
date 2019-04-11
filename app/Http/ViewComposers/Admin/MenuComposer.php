<?php

namespace App\Http\ViewComposers\Admin;

use AuthHelper;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use App\Models\Permission;

class MenuComposer
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function renderSub($child)
    {
        if (!in_array($child->permission, $this->request->session()->get('admin_permissions')) && !in_array('all', $this->request->session()->get('admin_permissions'))) {
            $item = '';
        } else {
            if ($child->status == 1 && AuthHelper::canShowWLMenu($child->permission)) {
                $item = '<li><a href="/admin/' . $child->permission . '">' . $child->title . '</a></li>';
            } else {
                $item = '';
            }
        }

        return $item;
    }

    public function renderMenu($node)
    {
        $item = '';

        if (!in_array($node->permission, $this->request->session()->get('admin_permissions')) && !in_array('all', $this->request->session()->get('admin_permissions'))) {
            return $item;
        }

        if ($node->children()->count() > 0) {
            $item .= '<li class="dropdown"><a>'; // Maybe check if is child?
        } else {
            $item .= '<li><a href="/admin/' . $node->permission . '">';
        }

        $item .= '<span><i class="fa fa-' . $node->icon_class . '"></i></span><p>' . $node->title . '</p></a>';

        if ($node->children()->count() > 0) {
            $item .= '<ul>';
            foreach ($node->children as $child) {
                $item .= $this->renderSub($child);
            }
            $item .= '</ul>';
        }

        $item .= '</li>';

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
        $menu = '';
        $menu .= '<ul class="menu">';
        $menu .= '<li><a href="/admin"><span><i class="fa fa-tachometer"></i></span><p>Dashboard</p></a></li>';

        $nodes = Permission::roots()->where('is_menu', 1)->get();
        foreach ($nodes as $node) {
            $menu .= $this->renderMenu($node);
        }

        $menu .= '</ul>';

        $view->with('menu', $menu);
    }
}
