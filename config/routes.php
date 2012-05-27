<?php
/**
 * ルーティングルールを記述する
 */
Router::setRule(
    array(
        '/' => "entry#entry",
        '/tag/:name' => "entry#tag",
        '/entry/:id' => "entry#id",
        '/archive/:ymd' => "entry#archive",
        '/hitorigoto' => "ajax#hitorigoto",
        '/feed' => "feed#rss",
        '/auth' => "login#auth",
        '/auth_back' => "login#auth_back",
        '/login' => "login#login",
        '/logout' => "login#logout",
        '/login_error' => "login#error",
        '/manage' => "manage#entry_list",
        '/entry_register' => "manage#entry_register",
        '/confirm' => "manage#confirm",
        '/register' => "manage#register",
        '/remand' => "manage#remand",
        '/delete' => "manage#delete",
        '/edit' => "manage#edit",
        '/tag_register' => "manage#tag_register",
        '/tag_list' => "ajax#tag_list",
        '/tag_delete' => "ajax#tag_delete",
        '/tag_register/:name' => "ajax#tag_register",
        '/entry_register_preview' => "manage#entry_register_preview",
        '/upload_image_register' => "manage#upload_image_register",
        '/amazon_search' => "manage#amazon_search",
        '/amazon_item/:keyword' => "ajax#amazon_item",
        '/images' => "manage#image_list",
        '/image_list/:page' => "ajax#image_list",
        '/temporary_save' => "ajax#temporary_save",
        '/temporary_load' => "ajax#temporary_load",
        
        
        //'/new_entry' => "manage#new_entry",
        //'/upload' => "manage#upload",
        //'/error' => "error#error",
        
        
        
        '/debug/:title' => "debug#run",
        '/debug/amazon/:keyword' => "debug#amazon"
        //'/archive_graph' => "archive#graph"
    )
);
