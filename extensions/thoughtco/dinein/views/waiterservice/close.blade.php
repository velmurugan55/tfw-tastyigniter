<div class="row-fluid">
    {!! form_open(current_url(),
        [
            'id'     => 'edit-form',
            'role'   => 'form',
            'method' => 'PATCH',
        ]
    ) !!}

    {!! $toolbarWidget->render() !!}

    <div id="form-outside-tabs">
        <div class="form-fields">

            <div class="form-group partial-field span-left">
                <div class="d-flex">
                    <div class="mr-3 flex-fill">
                        <label class="control-label">
                            @lang('admin::lang.orders.label_order_id')
                        </label>
                        <h3>{{ $table->table_name }}</h3>
                    </div>
                    <div class="mr-3 flex-fill">
                        <label class="control-label">
                            @lang('admin::lang.orders.label_total_items')
                        </label>
                        <h3>{{ $totalItems }}</h3>
                    </div>
                    <div class="flex-fill">
                        <label class="control-label">
                            @lang('admin::lang.orders.label_order_total')
                        </label>
                        <h3>{{ currency_format($orderTotal) }}</h3>
                    </div>
                </div>
            </div>

            {!! $this->makePartial('form/menus') !!}
        </div>
        @if($singleOrderItemDelete == 1)
        <div class="row">
            <div class="col-lg-1"></div>
            <div class="col-lg-11">
                <p><b>Since the order is in received status, the admin is allowed to delete item.</b></p>
                    @if($status_id == 1 || $status_id == 2)
                        {!! $this->makePartial('form/menulistorder') !!}
                    @endif
             </div>
        </div>
        @endif 
    </div>

    {!! form_close() !!}

    {!! $this->makePartial('form/modal') !!}

</div>
