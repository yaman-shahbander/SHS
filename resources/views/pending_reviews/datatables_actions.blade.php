<div class='btn-group btn-group-sm'>

{{--  @can('review.approve')--}}
  <a data-toggle="tooltip" data-placement="bottom" title="'review approve'" href="{{ route('reviews.approve',['id'=> $id]) }}" style="border-radius: 29px;
    border: 1px solid #0ec10e;" class='btn btn-link'>
      <i class="fa fa-check" style="color:#0ec10e;"></i>
    </a>
  
{{--  @endcan--}}

{{--  @can('review.approve')--}}
  <a data-toggle="tooltip" data-placement="bottom" title="'review approve'" href="{{ route('reviews.edit',['id'=> $id]) }}" class='btn btn-link'>
    <i class="fa fa-edit"></i>
  </a>
{{--  @endcan--}}




@can('reviews.destroy')
  {!! Form::open(['route' => ['reviews.destroy', $id], 'method' => 'delete']) !!}
  {!! Form::button('<i class="fa fa-trash"></i>', [
  'type' => 'submit',
  'class' => 'btn btn-link text-danger',
  'onclick' => "return confirm('Are you sure?')"
  ]) !!}
  {!! Form::close() !!}
@endcan

</div>