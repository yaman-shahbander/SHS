<div class='btn-group btn-group-sm'>
  @can('sales_man.show')
  <a data-toggle="tooltip" data-placement="bottom" title="{{trans('lang.view_details')}}" href="{{ route('subcategory.show', $id) }}" class='btn btn-link'>
    <i class="fa fa-eye"></i>
  </a>
  @endcan

  @can('sales_man.edit')
  <a data-toggle="tooltip" data-placement="bottom" title="{{trans('lang.delegate_edit')}}" 
  href="{{ route('sales_man.edit', $id) }}" class='btn btn-link'>
    <i class="fa fa-edit"></i>
  </a>
  @endcan

  @can('sales_man.destroy')
{!! Form::open(['route' => ['sales_man.destroy', $id], 'method' => 'delete']) !!}
  {!! Form::button('<i class="fa fa-trash"></i>', [
  'type' => 'submit',
  'class' => 'btn btn-link text-danger',
  'onclick' => "return confirm('Are you sure?')"
  ]) !!}
{!! Form::close() !!}
  @endcan
</div>
