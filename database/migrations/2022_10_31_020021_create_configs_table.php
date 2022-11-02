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
        Schema::create('configs', function (Blueprint $table) {
            $table->id()->startingValue(10000);
            $table->string('name', 50)->default('')->comment('配置标识')->unique();
            $table->string('label', 50)->default('')->comment('配置名称');
            $table->string('group', 50)->default('')->comment('分组');
            $table->string('type', 50)->default('')->comment('类型');
            $table->string('component', 50)->default('')->comment('渲染组件');
            $table->string('component_props')->default('')->comment('渲染组件props参数');
            $table->string('enum')->default('')->comment('枚举项');
            $table->string('value')->default('')->comment('配置值');
            $table->string('validate')->default('')->comment('验证规则');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
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
        Schema::dropIfExists('configs');
    }
};
