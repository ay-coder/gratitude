<div class="box-body">
    <div class="form-group">
        {{ Form::label('blocked_by', 'Blocked By :', ['class' => 'col-lg-2 control-label']) }}
        <div class="col-lg-10">
            {{ Form::text('blocked_by', null, ['class' => 'form-control', 'placeholder' => 'Blocked By', 'required' => 'required']) }}
        </div>
    </div>
</div><div class="box-body">
    <div class="form-group">
        {{ Form::label('user_id', 'User Id :', ['class' => 'col-lg-2 control-label']) }}
        <div class="col-lg-10">
            {{ Form::text('user_id', null, ['class' => 'form-control', 'placeholder' => 'User Id', 'required' => 'required']) }}
        </div>
    </div>
</div><div class="box-body">
    <div class="form-group">
        {{ Form::label('post_id', 'Post Id :', ['class' => 'col-lg-2 control-label']) }}
        <div class="col-lg-10">
            {{ Form::text('post_id', null, ['class' => 'form-control', 'placeholder' => 'Post Id', 'required' => 'required']) }}
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