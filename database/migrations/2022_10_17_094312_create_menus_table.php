<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pid')->default(0)->comment('父级ID');
            $table->string('name', 50)->default('')->comment('路由别名')->unique();
            $table->string('label', 50)->default('')->comment('显示名称');
            $table->string('type', 50)->default('')->comment('菜单类型');
            $table->string('icon', 50)->default('')->comment('图标');
            $table->string('path')->default('')->comment('路由地址');
            $table->string('component')->default('')->comment('组件');
            $table->integer('sort')->default(0)->comment('排序');
            $table->boolean('keepalive')->default(true)->comment('是否缓存');
            $table->boolean('affix')->default(false)->comment('是否固定标签栏');
            $table->boolean('hide_menu')->default(false)->comment('是否显示菜单');
            $table->boolean('status')->default(true)->comment('状态');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menus');
    }
};
