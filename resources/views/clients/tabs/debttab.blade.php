<div id="debt" class="tab-pane fade">
    <div class="boxspace">
        <table class="table table-hover">
            <h4>{{ __('All Debts') }}</h4>
            <thead>
            <thead>
            <tr>
                <th>{{ __('Title') }}</th>
                <th>{{ __('Assigned user') }}</th>
                <th>{{ __('Created at') }}</th>

                <th><a href="{{ route('debts.create', ['client' => $client->id])}}">
                        <button class="btn btn-success">{{ __('New Debt') }}</button>
                    </a></th>

            </tr>
            </thead>
            <tbody>
            <?php  $tr = ""; ?>
          
            @foreach($client->debts as $debt)
                @if($debt->status == 1)
                    <?php  $tr = '#adebad'; ?>
                @elseif($debt->status == 2)
                    <?php $tr = '#ff6666'; ?>
                @endif
                <tr style="background-color:<?php echo $tr;?>">

                    <td><a href="{{ route('debts.show', $debt->id) }}">{{$debt->title}} </a></td>
                    <td>
                        <div class="popoverOption"
                             rel="popover"
                             data-placement="left"
                             data-html="true"
                             data-original-title="<span class='glyphicon glyphicon-user' aria-hidden='true'> </span> {{$debt->user->name}}">
                            <div id="popover_content_wrapper" style="display:none; width:250px;">
                                <img src='http://placehold.it/350x150' height='80px' width='80px'
                                     style="float:left; margin-bottom:5px;"/>
                                <p class="popovertext">
                                    <span class="glyphicon glyphicon-envelope" aria-hidden="true"> </span>
                                    <a href="mailto:{{$debt->user->email}}">
                                        {{$debt->user->email}}<br/>
                                    </a>
                                    <span class="glyphicon glyphicon-headphones" aria-hidden="true"> </span>
                                    <a href="mailto:{{$debt->user->work_number}}">
                                    {{$debt->user->work_number}}</p>
                                </a>

                            </div>
                            <a href="{{route('users.show', $debt->user->id)}}"> {{$debt->user->name}}</a>

                        </div> <!--Shows users assigned to debt -->
                    </td>
                    <td>{{date('d, M Y, H:i', strTotime($debt->created_at))}}  </td>
                    <td></td>
                </tr>

            @endforeach

            </tbody>
        </table>
    </div>
</div>
