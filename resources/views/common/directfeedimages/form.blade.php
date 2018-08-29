<div class="box-body">
    <div class="form-group">
        {{ Form::label('feed_id', 'Feed Id :', ['class' => 'col-lg-2 control-label']) }}
        <div class="col-lg-10">
            {{ Form::text('feed_id', null, ['class' => 'form-control', 'placeholder' => 'Feed Id', 'required' => 'required']) }}
        </div>
    </div>
</div><div class="box-body">
    <div class="form-group">
        {{ Form::label('image', 'Image :', ['class' => 'col-lg-2 control-label']) }}
        <div class="col-lg-10">
            {{ Form::text('image', null, ['class' => 'form-control', 'placeholder' => 'Image', 'required' => 'required']) }}
        </div>
    </div>
</div>