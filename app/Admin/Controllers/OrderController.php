<?php

namespace App\Admin\Controllers;

use App\OrderModel;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class OrderController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Index')
            ->description('description')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Edit')
            ->description('description')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new OrderModel);

        $grid->o_id('O id');
        $grid->u_id('U id');
        $grid->order_sn('Order sn');
        $grid->order_amount('Order amount');
        $grid->pay_amount('Pay amount');
        $grid->add_time('Add time');
        $grid->pay_time('Pay time');
        $grid->is_delete('Is delete');

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(OrderModel::findOrFail($id));

        $show->o_id('O id');
        $show->u_id('U id');
        $show->order_sn('Order sn');
        $show->order_amount('Order amount');
        $show->pay_amount('Pay amount');
        $show->add_time('Add time');
        $show->pay_time('Pay time');
        $show->is_delete('Is delete');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new OrderModel);

        $form->number('o_id', 'O id');
        $form->number('u_id', 'U id');
        $form->text('order_sn', 'Order sn');
        $form->number('order_amount', 'Order amount');
        $form->number('pay_amount', 'Pay amount');
        $form->number('add_time', 'Add time');
        $form->number('pay_time', 'Pay time');
        $form->switch('is_delete', 'Is delete');

        return $form;
    }
}
