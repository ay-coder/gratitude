<div class="box-body">
    <div class="form-group">
        {{ Form::label('user_id', 'User Id :', ['class' => 'col-lg-2 control-label']) }}
        <div class="col-lg-10">
            {{ Form::text('user_id', null, ['class' => 'form-control', 'placeholder' => 'User Id', 'required' => 'required']) }}
        </div>
    </div>
</div><div class="box-body">
    <div class="form-group">
        {{ Form::label('feed_id', 'Feed Id :', ['class' => 'col-lg-2 control-label']) }}
        <div class="col-lg-10">
            {{ Form::text('feed_id', null, ['class' => 'form-control', 'placeholder' => 'Feed Id', 'required' => 'required']) }}
        </div>
    </div>
</div><div class="box-body">
    <div class="form-group">
        {{ Form::label('comment_id', 'Comment Id :', ['class' => 'col-lg-2 control-label']) }}
        <div class="col-lg-10">
            {{ Form::text('comment_id', null, ['class' => 'form-control', 'placeholder' => 'Comment Id', 'required' => 'required']) }}
        </div>
    </div>
</div><div class="box-body">
    <div class="form-group">
        {{ Form::label('comment', 'Comment :', ['class' => 'col-lg-2 control-label']) }}
        <div class="col-lg-10">
            {{ Form::text('comment', null, ['class' => 'form-control', 'placeholder' => 'Comment', 'required' => 'required']) }}
        </div>
    </div>
</div>