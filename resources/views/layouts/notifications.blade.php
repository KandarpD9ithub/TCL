
@if (count($errors->all()) > 0)

    <div class="alert alert-danger margintop10">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <div>
            <span class="glyphicon glyphicon-exclamation-sign"></span>
            <b>Please fix the following input errors :</b>
        </div>
        <?php $requiredCount = 0; $errorMessages = ''; ?>

        @foreach ($errors->all() as $error)
            @if (preg_match('[required]', $error))
                <?php $requiredCount++; ?>
            @endif

            <?php $errorMessages .= $error.'<br/>'; ?>
        @endforeach

        <?php
        if ($requiredCount > 1) {
            echo 'Please fill up the required fields.';
        } else {
            echo $errorMessages;
        }
        ?>
    </div>
@endif

@if ($message = Session::get('success'))
    <div class="alert alert-success alert-block margintop10">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <span class="glyphicon glyphicon-ok-sign"></span>
        @if(is_array($message))
            @foreach ($message as $m)
                {{ $m }}
            @endforeach
        @else
            {{ $message }}
        @endif
    </div>
@endif

@if ($message = Session::get('error'))
    <div class="alert alert-danger alert-block margintop10">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <span class="glyphicon glyphicon-exclamation-sign"></span>
        @if(is_array($message))
            @foreach ($message as $m)
                {{ $m }}
            @endforeach
        @else
            {{ $message }}
        @endif
    </div>
@endif

@if ($message = Session::get('warning'))
    <div class="alert alert-warning alert-block margintop10">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <span class="glyphicon glyphicon-warning-sign"></span>
        @if(is_array($message))
            @foreach ($message as $m)
                {{ $m }}
            @endforeach
        @else
            {{ $message }}
        @endif
    </div>
@endif

@if ($message = Session::get('info'))
    <div class="alert alert-info alert-block margintop10">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <span class="glyphicon glyphicon-info-sign"></span>
        @if(is_array($message))
            @foreach ($message as $m)
            {{ $m }}
            @endforeach
        @else
            {{ $message }}
        @endif
    </div>
@endif
