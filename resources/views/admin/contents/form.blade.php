<div class="is-row">
    <div class="is-col is-75">
        <nav class="tabs" data-kube="tabs" data-equal="true">
            <a href="#infos" class="is-active">{{ __('piclommerce::admin.informations') }}</a>
            <a href="#medias">{{ __('piclommerce::admin.medias') }}</a>
            <a href="#seo">{{ __('piclommerce::admin.seo') }}</a>
        </nav>

        <section id="infos">
            <div class="is-row">
                <div class="is-col">
                    <div class="form-item">
                        <label class="checkbox">
                            <input type="checkbox" name="published" value="1" {!! (!empty($data->published))?'checked="checked"':'' !!}>
                            {{ __('piclommerce::admin.online') }} ?
                        </label>
                    </div>
                </div>
                <div class="is-col">
                    <div class="form-item">
                        <label class="checkbox">
                            <input type="checkbox" name="on_homepage" value="1" {!! (!empty($data->on_homepage))?'checked="checked"':'' !!}>
                            {{ __('piclommerce::admin.content_pages_on_homepage') }} ?
                        </label>
                    </div>
                </div>
                <div class="is-col">
                    <div class="form-item">
                        <label class="checkbox">
                            <input type="checkbox" name="on_menu" value="1" {!! (!empty($data->on_menu))?'checked="checked"':'' !!}>
                            {{ __('piclommerce::admin.content_pages_on_menu') }} ?
                        </label>
                    </div>
                </div>
                <div class="is-col">
                    <div class="form-item">
                        <label class="checkbox">
                            <input type="checkbox" name="on_footer" value="1" {!! (!empty($data->on_footer))?'checked="checked"':'' !!}>
                            {{ __('piclommerce::admin.content_pages_on_footer') }} ?
                        </label>
                    </div>
                </div>
            </div>
            <div class="is-row">
                <div class="is-col">
                    <div class="form-item">
                        <label for="form-name">{{ __('piclommerce::admin.content_pages_name') }}</label>
                        <input type="text" name="name" id="form-name" value="{{ $data->name }}">
                    </div>
                </div>
            </div>

            <div class="form-item">
                <label form="form-content_category_id">{{ __('piclommerce::admin.content_categories_name') }}</label>
                <select id="form-content_category_id" name="content_category_id">
                    <option value="">{{ __('piclommerce::admin.content_pages_choose') }}</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {!! ($data->content_category_id == $category->id)?'selected="selected"':'' !!}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="form-item">
                <label for="form-summary">{{ __('piclommerce::admin.content_pages_summary') }}</label>
                <textarea class="html-editor" cols="0" rows="0" name="summary" id="form-summary">{{ $data->summary }}</textarea>
            </div>

            <div class="form-item">
                <label for="form-description">{{ __('piclommerce::admin.content_pages_description') }}</label>
                <textarea class="html-editor" cols="0" rows="0" name="description" id="form-description">{{ $data->description }}</textarea>
            </div>

        </section>

        <section id="medias">
            <div class="is-row">
                <div class="is-col is-75">
                    <table class="is-bordered is-striped is-responsive">
                        <thead>
                        <tr>
                            <th>
                                {{ __('piclommerce::admin.medias_image') }}
                            </th>
                            <th>
                                {{ __('piclommerce::admin.medias_title') }}
                            </th>
                            <th>
                                {{ __('piclommerce::admin.medias_description') }}
                            </th>
                            <th>
                                {{ __('piclommerce::admin.medias_type') }}
                            </th>
                            <th>

                            </th>
                        </tr>
                        </thead>
                        <tbody>
                            @if(!$data->image)
                                <tr>
                                    <td colspan="5">{{ __('piclommerce::admin.no_data') }}</td>
                                </tr>
                            @else
                                @php $medias = $data->getMedias("image"); @endphp
                                <tr>
                                    <td data-label="{{ __('piclommerce::admin.medias_image') }}">
                                        <img src="{{  $data->resizeImage("image", 30 ,30)['target_path'] }}"
                                             alt="{{ $medias['alt'] }}"
                                             class="remodalImg"
                                             data-src="/{{ $medias['target_path'] }}"
                                        >
                                    </td>
                                    <td data-label="{{ __('piclommerce::admin.medias_title') }}">
                                        <input type="text" name="medias_alt" value="{{ $medias['alt'] }}">
                                    </td>
                                    <td data-label="{{ __('piclommerce::admin.medias_description') }}">
                                        <input type="text" name="medias_description" value="{{ $medias['description'] }}">
                                    </td>
                                    <td data-label="{{ __('piclommerce::admin.medias_type') }}">
                                        {{ $medias['file_type'] }}
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.pages.content.image.update',['uuid' => $data['uuid']]) }}"
                                           class="table-button edit-media"
                                        >
                                            <i class="fa fa-floppy-o"></i> {{ __("piclommerce::admin.edit") }}
                                        </a>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="is-col is-25">
                    <label for="form-file">
                        {{ __('piclommerce::admin.medias_single_upload') }}
                    </label>
                    <input type="file" name="image" id="form-file">
                </div>
            </div>

        </section>

        <section id="seo">
            @include("piclommerce::components.admin.seo.seo_form")
        </section>

    </div>

    <div class="is-col is-25">
        @include("piclommerce::components.admin.seo.seo_optimisation")
    </div>
</div>