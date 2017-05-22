@extends('layouts.app')

@section('css')
    <link rel="stylesheet" type="text/css" href="/css/home.css">
@endsection

@section('content')
    <div class="container">
        <div class="row col-lg-12">

            <form class="search-form" role="search" action="{{ route('roots') }}" method="get">
                <div class="input-group">
                    <input type="text" name="data" id="data" placeholder="Recherche" class="form-control input-lg" ng-model="name">
                    <a  class="input-group-addon w3-blue"><i class="fa fa-search fa-fw" aria-hidden="true"></i></a>
                </div>
            </form>
        </div>

        <div class="row col-lg-12">
            <hr class="w3-blue w3-text-blue" />
        </div>
        <h4>Don't forget to handle the download of all PDFS</h4>
        <h4>Pagination</h4>
        <h4>Filter by new date</h4>
        

        <!-- A voir l'optimisation du Code-->
        @if(!empty($params['start']) and count($params) >= 1)
                @php 
                    $params['start'] = 1; 
                @endphp
        @endif
        <div class="row col-lg-12 w3-padding-4 w3-margin-bottom w3-blue">
            <h3>Tous les articles</h3>
            @php print_r($numberss); @endphp
        </div>
        <div class="row w3-margin-bottom">
            <h5>Nombres d'articles : <i>{{ $resultset->getNumFound() }} </i></h5>
            <h4>Time : {{$resultset->getQueryTime()}}</h4>
        </div>
            <div>


        @if(!empty($params['data']))
            {{$user_id}}
            {{ route('roots',$request->all()) }}
            <br><strong>Sort par : </strong><br>
            <select id="comboA" onchange="getComboA(this)">
                @if(!empty($params['sort']) and $params['sort'] == 'pertinence')
                    <option value='pertinence' selected="selected">Relevance</option>
                @elseif(empty($params['sort']) or $params['sort'] != 'pertinence')
                    <option value='pertinence'>Relevance</option>
                @endif

                @if(!empty($params['sort']) and $params['sort'] == 'date')
                    <option value='date' selected="selected">Newest</option>
                @elseif(empty($params['sort']) or $params['sort'] != 'date')
                    <option value='date'>Newest</option>
                @endif
                
            </select>
        @endif
        <!-- This is custom facade, so I have to code everything-->
        <br><strong>Filter by keywords: </strong><br>
        @foreach($numberss as $key => $count)
            @if (empty($params['keyword']))
                @if($count > 0)
                <a href="{{ route('roots', $params) }}{{$sign}}keyword={{$key}}">{{ $key.'['.$count.']' }}</a><br>
                @endif
            @elseif ($params['keyword'] == $key)
                {{ $key.'['.$count.']' }}<br>
            @endif
        @endforeach
                  
        <strong>Filter by Language :</strong><br>
        @foreach ($facet1 as $value => $count)
            @if (empty($params['language']))
            <a href="{{ route('roots', $params) }}{{$sign}}language={{$value}}">{{ $value.'['.$count.']' }}</a><br>
            @elseif ($params['language'] == $value)
                {{ $value.'['.$count.']' }}<br>
            @endif
        @endforeach

        <strong>Les derniers 7 jours :</strong><br>
        <form class="search-form" role="search" action="{{ route('roots') }}" method="get">
                <div class="input-group">
                    <input type="text" name="fromdate" id="data" placeholder="date1" class="form-control input-lg" ng-model="name" value="2017-02-04"><br>
                   <input type="text" name="todate" id="data" placeholder="date2" class="form-control input-lg" ng-model="name" value="2017-05-15">
                </div>
                <input type="submit" class="btn btn-primary" value="send">
            </form>

        <!--
        // année
            // mois
                // semaine
-->
<!--
        <strong>Filter by Author :</strong><br>
        @foreach ($facet2 as $value => $count)
            @if (empty($params['author']))
                
                <a href="{{ route('roots', $params) }}{{$sign}}author={{$value}}">{{ $value.'['.$count.']' }}</a><br>
            @elseif ($params['author'] == $value)
                {{ $value.'['.$count.']' }}<br>
            @endif
        @endforeach
    -->    
        <strong>Filter by Source :</strong><br>
        @foreach ($facet3 as $value => $count)
           @if (empty($params['source']))

                <a href="{{ route('roots', $params)}}{{$sign}}source={{$value}}">{{ $value.'['.$count.']' }}</a><br>
             @elseif ($params['source'] == $value)
                {{ $value.'['.$count.']' }}<br>
            @endif
        @endforeach

        @php 
            $highlighting = $resultset->getHighlighting(); 
            $pdfs = [];
            $titles = [];
        @endphp

        @foreach ($resultset as $document)
            @php
                $highlightedDoc = $highlighting->getResult($document->id);
                $pdf = $document->document;
                $pdf1 = str_replace('/home/harbouj/laravel/app/public', '', $pdf);
                echo $pdf1;
                $datesolr = substr($document->SourceDate,0,10);
               $timess = strtotime($datesolr);

               $date = date("d-m-Y", $timess);

               if(!empty($document->Title))
                    $title = $document->Title;

              if(!empty($document->Title_en))
                    $title = $document->Title_en;
              if(!empty($document->Title_fr))
                    $title = $document->Title_fr;
            if(!empty($document->Title_ar))
                    $title = $document->Title_ar;
            @endphp

                <div class="row w3-margin-bottom">
                           
                    <div class="media">
                      <div class="media-left">
                        <img src="img_avatar1.png" class="media-object" style="width:90px">
                      </div>
                      <div class="media-body">
                        <h4 class="media-heading w3-xlarge">
                        <a href="{{ route('articles.show', ['id' => $document->id]) }}">
                            {!! (count($highlightedDoc->getField('Title'))) ? implode(' ... ', $highlightedDoc->getField('Title')) : $document->Title !!}
                            {!! (count($highlightedDoc->getField('Title_en'))) ? implode(' ... ', $highlightedDoc->getField('Title_en')) : $document->Title_en !!}
                            {!! (count($highlightedDoc->getField('Title_fr'))) ? implode(' ... ', $highlightedDoc->getField('Title_fr')) : $document->Title_fr !!}
                            {!! (count($highlightedDoc->getField('Title_ar'))) ? implode(' ... ', $highlightedDoc->getField('Title_ar')) : $document->Title_ar !!}
                        </a>

                        </h4>
                        <i>Publié le {{ $date }}</i>
                        <p>
                       
                            {!! (count($highlightedDoc->getField('Fulltext'))) ? implode(' ... ', $highlightedDoc->getField('Fulltext')) : substr($document->Fulltext,0,300) !!}
                            {!! (count($highlightedDoc->getField('Fulltext_en'))) ? implode(' ... ', $highlightedDoc->getField('Fulltext_en')) : substr($document->Fulltext_en,0,300) !!}
                            {!! (count($highlightedDoc->getField('Fulltext_fr'))) ? implode(' ... ', $highlightedDoc->getField('Fulltext_fr')) : substr($document->Fulltext_fr,0,300) !!}
                            {!! (count($highlightedDoc->getField('Fulltext_ar'))) ? implode(' ... ', $highlightedDoc->getField('Fulltext_ar')) : substr($document->Fulltext_ar,0,300) !!}
                        </p>
                        <i>Source : {{ $document->Source }}</i><br>
                        
                        <i>Auteur : {{ $document->Author }}</i>
                        @if(!empty($params['data']))
                            <br><i>Score {{ $document->score }}</i>
                        @endif
                        <a href="{{ $pdf1 }}" target="_blank" class="w3-btn pull-right w3-green">Voir le pdf</a>
                      </div>
                    </div>

                </div>
                @php 
                    array_push($pdfs, $pdf);
                    array_push($titles, $title);
                    
                @endphp
        @endforeach
        @php 
        print_r($titles);
            $num = $resultset->getNumFound() / 10;
            $num++;
            $endpag = (int) $num;
        @endphp
        {{(int) $num}}

    @php
        $starts = 1;
        if ($num < 10)
            $end = $endpag;
        else
            $end = 10;
        
        echo $num;
    if(!empty($request->start)) {
        if($request->start - 5 >= 1) {
            $starts = $request->start - 5;
        }

        if ($request->start + 4 >= 10 ) { 
            $end = $request->start + 4;
        } 
        if ($request->start + 4 > $endpag)
            $end = $request->start + ($endpag - $request->start);

        if ($request->start >= (int) $num) {
            $end = $num;
        }

    }
    @endphp
  <nav aria-label="Page navigation">
  <ul class="pagination">
    <li>
      <a href="#" aria-label="Previous">
        <span aria-hidden="true">&laquo;</span>
      </a>
    </li>
    @for ($i = $starts; $i <= $end; $i++)
            @php $params['start'] = $i; @endphp

            @if($request->start == $i)
                <li class="page-item active">
                    <a class="page-link" href="#">{{$i}} <span class="page-link sr-only">(current)</span></a>
                </li>
            @else 
                <li><a href="{{ route('roots', $params) }}">{{$i}}</a></li>
            @endif

    @endfor
    <li>
      <a href="#" aria-label="Next">
        <span aria-hidden="true">&raquo;</span>
      </a>
    </li>
  </ul>
</nav>
<a href="{{ route('articles.test', ['pdf' => $pdfs, 'titles' => $titles]) }}" target="_blank" class="w3-btn pull-left w3-green">Télécharger tous les PDF</a>
    </div>
<script>
$('.datepicker').datepicker();

$('.input-daterange input').each(function() {
    $(this).datepicker('clearDates');
});
    function getComboA(selectObject) {
        var value = selectObject.value;
        
          
        //console.log( getParameterByName('data'));

        window.location =  '?data='+getParameterByName('data')+'&sort='+value;
    }
    function getParameterByName(name, url) {
        if (!url) url = window.location.href;
        name = name.replace(/[\[\]]/g, "\\$&");
        var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, " "));
}
</script>
@endsection
