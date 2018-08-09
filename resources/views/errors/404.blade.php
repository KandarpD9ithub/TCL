
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <h2 class="text-center">404</h2>
                        <div class="text-center">
                            <h4>
                                <i class="glyphicon glyphicon-warning-sign"></i>
                                {{ Lang::get('errors.page_not_found') }}
                            </h4>
                            <p>
                                {{ Lang::get('errors.404_line1') }}
                            </p>
                            <p>
                                {{ Lang::get('errors.404_line2') }}
                                <a href="{{ URL::to('/') }}">
                                    {{ Lang::get('errors.home_page') }}
                                </a>?
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>