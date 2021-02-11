{!! Form::open(['route' => ['vendor.destroy', $id], 'method' => 'delete']) !!}
<div class='btn-group btn-group-sm'>
    <a data-toggle="tooltip" data-placement="bottom" title="User approve" href="{{ route('vendor.show', $id) }}" class='btn btn-link'>
        <i class="fa fa-check"></i> </a>
        <a data-toggle="tooltip" data-placement="bottom" title="Banned User" class='btn btn-link' href="{{ route('banned', $id)}}"><i class="fa fa-ban  mr-1"></i></a>

    {!! Form::button('<i class="fa fa-trash"></i>', [
    'data-toggle' => 'tooltip',
    'data-placement' => 'bottom',
    'title' => trans('lang.vendor_delete'),
    'type' => 'submit',
    'class' => 'btn btn-link text-danger',
    'onclick' => "swal({title: ".trans('lang.error').", confirmButtonText: ".trans('lang.ok').",
                            text: data.message,type: 'error', confirmButtonClass: 'btn-danger'});"
    ]) !!}
    



</div>
{!! Form::close() !!}