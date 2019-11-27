<?php

use think\facade\Route;

Route::group(function(){
    // 用户管理
    Route::get('/user', 'User@index')->name('admin.user.index');
    Route::delete('/user/<id>', 'user/delete')->name('admin.user.delete');
    // 话题管理
    Route::get('/topic', 'Topic@index')->name('admin.topic.index');
    Route::delete('/topic/<id>', 'topic/delete')->name('admin.topic.delete');
    // 回复管理
    Route::get('/reply', 'Reply@index')->name('admin.reply.index');
    Route::delete('/reply/<id>', 'reply/delete')->name('admin.reply.delete');
})->middleware(['tpadmin.admin', 'tpadmin.admin.role'])->prefix('\\app\\admin\\controller\\');
