@extends("piclommerce::layouts.app")

@section("content")
    <div class="head-title">
        <div class="l-container">
            <h1>{{ __("piclommerce::web.contact_title") }}</h1>
        </div>
    </div>
    @include('piclommerce::components.search-bar')
    <div class="l-container contact-section">
        <form method="post" action="{{ route("contact.send") }}">
            {{ csrf_field() }}
            @if(count($errors) > 0)
                @include("piclommerce::components.alert-error")
            @endif

            <div class="form-item">
                <label>{{ __('piclommerce::web.user_firstname') }}</label>
                <input type="text" name="firstname" required="required" value="">
            </div>

            <div class="form-item">
                <label>{{ __('piclommerce::web.user_lastname') }}</label>
                <input type="text" name="lastname" required="required" value="">
            </div>

            <div class="form-item">
                <label>{{ __('piclommerce::web.user_email') }}</label>
                <input type="email" name="email" required="required" value="">
            </div>

            <div class="form-item">
                <label>{{ __('piclommerce::web.contact_message') }}</label>
                <textarea cols="0" rows="0" name="message" required="required"></textarea>
            </div>

            <button type="submit">
                {{ __("piclommerce::web.send") }}
            </button>

        </form>
    </div>

    @if(count($contents) > 0)
        <div class="home-contents">
            <div class="l-container">
                <div class="is-row is-col-stack-20">
                    @foreach($contents as $content)
                        <div class="is-col">
                            @if($content->image)
                                <div class="content-image">
                                    @php $medias = $content->getMedias("image"); @endphp
                                    @if($medias)
                                        <img src="{{ resizeImage($medias['target_path'], null, 280) }}"
                                             alt="{{ $medias['alt'] }}"
                                                {!! ($medias['description'])?'title="'.$medias['description'].'"':'' !!}
                                        >
                                    @endif
                                </div>
                            @endif
                            <h5>{{ $content->name }}</h5>
                            {!! $content->summary !!}
                            <a href="{{ route('content.index',[ 'slug' => $content->slug, 'id' => $content->id ]) }}" class="read-more">
                                {{ __('piclommerce::web.read_more') }}
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

@endsection