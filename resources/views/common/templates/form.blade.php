<div class="box-body">
    <div class="form-group">
        {{ Form::label('body', 'Body :', ['class' => 'col-lg-2 control-label']) }}
        <div class="col-lg-10">
            {{ Form::text('body', null, ['class' => 'form-control', 'placeholder' => 'Body', 'required' => 'required']) }}
        </div>
    </div>
</div>