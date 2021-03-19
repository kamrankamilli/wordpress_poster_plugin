<?php

/**
* @package  Poster
*/

class VKSettings
{
    public $admin_pages = array();

    public $admin_sub_pages = array();

    //function to create on call
    function register()
    {
        if (!empty($this->admin_pages)) {
            add_action('admin_menu', array($this, 'addAdminMenu'));
        }
    }

    //add pages to the admin pages array
    function addPages(array $pages)
    {
        $this->admin_pages = $pages;

        return $this;
    }

    function subPage($title = null)
    {
        if (empty($this->admin_pages)) {
            return $this;
        }

        $admin_page = $this->admin_pages[0];


        $sub_page = array(
            array(
                'parent_slug' => $admin_page['menu_slug'],
                'page_title' => $admin_page['page_title'],
                'menu_title' => ($title) ? $title : $admin_page['menu_title'],
                'capability' => $admin_page['capability'],
                'menu_slug' => $admin_page['menu_slug'],
                'callback' => $admin_page['callback']
            )
        );

        $this->admin_sub_pages = $sub_page;

        return $this;
    }

    function addSubPages(array $pages)
    {
        $this->admin_sub_pages = array_merge($this->admin_sub_pages, $pages);

        return $this;
    }

    function addAdminMenu()
    {
        foreach ($this->admin_pages as $page) {
            add_menu_page($page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['callback'], $page['icon_url'], $page['position']);
        }
        foreach ($this->admin_sub_pages as $page) {
            add_submenu_page($page['parent_slug'], $page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['callback']);
        }
    }

}
