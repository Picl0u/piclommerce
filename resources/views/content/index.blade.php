@extends("piclommerce::layouts.app")

@section("content")
    <div class="head-title">
        <div class="l-container">
            <h1>{{ $content->name }}</h1>
        </div>
    </div>
    @include('piclommerce::components.search-bar')
    <div class="l-container content-section">
        @if(!is_null($category))
            <div class="is-row">
                <div class="is-col is-25 sidebar">
                    <h2>{{ $category->name }}</h2>
                    <nav class="sidebar-navigation">
                        <ul>
                        @foreach($contentList as $c)
                            <li>
                                <a href="{{ Route(
                                    'content.index',[
                                        'slug' => $c->slug,
                                        'id' => $c->id
                                    ])}}">{{ $c->name }}</a>
                            </li>
                        @endforeach
                        </ul>
                    </nav>
                </div>
                <div class="is-col is-75 content">
                    <div class="title">
                        <h3>{{ $content->name }}</h3>
                    </div>
                    {!! $content->description !!}
                    @if((is_null($content->on_homepage) || empty($content->on_homepage)) && !empty($content->image))
                        <div class="content-image">
                            @php $medias = $content->getMedias("image"); @endphp
                            @if($medias)
                                <div class="is-col banner">
                                    <img src="{{ $medias['target_path'] }}" alt="{{ $medias['alt'] }}">
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

        @else
            <div class="content">
                <div class="title">
                    <h3>{{ $content->name }}</h3>
                </div>
                {!! $content->description !!}
                @if((is_null($content->on_homepage) || empty($content->on_homepage)) && !empty($content->image))
                    <div class="content-image">
                        @php $medias = $content->getMedias("image"); @endphp
                        @if($medias)
                            <div class="is-col banner">
                                <img src="{{ $medias['target_path'] }}" alt="{{ $medias['alt'] }}">
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @endif
    </div>
@endsection