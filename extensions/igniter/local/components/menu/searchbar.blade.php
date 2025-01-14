<div class="py-3 px-4 border-top border-bottom">
<?php 
	$url = current_url();
	$url = str_replace("https://borderparotta.touchfreewaiter.in","",$url);
	$url = str_replace("//","/",$url);
    ?>
    <form
        id="menu-search"
        method="GET"
        role="form"
        action="{{ $url }}"
    >
        <div class="input-group">
            <div class="input-group-prepend">
                @if (strlen($menuSearchTerm))
                    <a
                        class="btn btn-light"
                        href="{{ $url }}"
                    ><i class="fa fa-times"></i></a>
                @else
                    <span class="input-group-text"><i class="fa fa-search"></i></span>
                @endif
            </div>
            <input
                type="search"
                class="form-control"
                name="q"
                placeholder="@lang('igniter.local::default.label_menu_search')"
                value="{{ $menuSearchTerm }}"
                autocomplete="off"
            >
        </div>
    </form>
</div>
