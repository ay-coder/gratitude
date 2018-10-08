<div class="box-body">
    <div class="form-group">
        {{ Form::label('title', 'Title :', ['class' => 'col-lg-2 control-label']) }}
        <div class="col-lg-10">
            {{ Form::text('title', null, ['class' => 'form-control', 'placeholder' => 'Title', 'required' => 'required']) }}
        </div>
    </div>
</div>

<div class="box-body">
    <div class="form-group">
        {{ Form::label('icon', 'Icon :', ['class' => 'col-lg-2 control-label']) }}
        <div class="col-lg-6">
            {{ Form::file('icon', null, ['class' => 'form-control', 'required' => 'required']) }}
        </div>

        <div class="col-lg-4 text-center">
            
            @if(isset($item->icon))
                {{ Html::image('/uploads/categories/'.$item->icon, 'icon', ['width' => 150, 'height' => 150]) }}
            @endif
        </div>
    </div>
</div>

