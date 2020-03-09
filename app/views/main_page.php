
<!DOCTYPE html>
<html>
<head>
    <title></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script
            src="https://code.jquery.com/jquery-3.4.1.min.js"
            integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
            crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.1/dist/jquery.validate.min.js"></script>
    <style>
        .content {
            max-width: 500px;
            margin: 0 auto;
            padding: 10px;
        }
        .content form {
            margin-top: 50px;
            position: relative;
        }
        .form-curtain {
            position: absolute;
            z-index: 12;
            background: #fff;
            width: 100%;
            height: 100%;
            opacity: 0.5;
        }
        .content #result {
            padding: 10px 0;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="content">
        <form>
            <div class="form-curtain d-none"></div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label">URL: </label>
                <div class="col-sm-8">
                    <input class="form-control" required name="url">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label">HTML element: </label>
                <div class="col-sm-8">
                    <input class="form-control" required name="element">
                </div>
            </div>
            <button class="btn btn-primary mt-3" type="submit">Submit</button>
        </form>
        <div id="result" class="d-none">
            <div>RESULT</div>
            <div>
                <div>URL {{request.url}}</div>
                <div>Fetched on {{request.time}}, took {{request.duration}} msec</div>
                <div>Element <{{request.element}}> appeared {{request.elementCount}} times in page</div>
            </div>
            <div class="mt-3">
                <div>General Statistics</div>
                <div>{{general.domainUrlsChecked}} different URLs from {{request.domain}} have been fetched</div>
                <div>Average fetch time from {{request.domain}} during the last 24 hours hours is {{general.avgRequestDuration}} ms</div>
                <div>There was a total of {{general.elementCountOnDomain}} <{{request.element}}> elements from {{request.domain}}</div>
                <div>Total of {{general.elementCountAllRequests}} <{{request.element}}> elements counted in all requests ever made</div>
            </div>
        </div>
    </div>
    <script>
        $(function () {
            var $form = $('form');
            var $curtain = $form.find('.form-curtain');
            var $resultContainer = $('#result');
            var resultTemplate = $resultContainer.html();
            $form.validate({
                rules: {
                    url: {
                        required: true,
                        url: true,
                    },
                    element: {
                        required: true,
                    },
                },
                submitHandler: function () {
                    $curtain.removeClass('d-none');
                    $.ajax({
                        url: '/fetch',
                        data: $form.find('input').serialize(),
                        dataType: 'json',
                    })
                        .done(function (res) {
                            var newHtml = resultTemplate;
                            var iterateKeys = function (obj, path) {
                                Object.keys(obj).forEach(function (key) {
                                    var value = obj[key];
                                    var currentPath = path ? path + '.' + key : key;
                                    if (typeof value === 'object') {
                                        iterateKeys(value, currentPath);
                                    } else {
                                        newHtml = newHtml.replace(new RegExp('{{' + currentPath + '}}', 'g'), value);
                                    }
                                });
                            };
                            iterateKeys(res);
                            $resultContainer.html(newHtml);
                            $resultContainer.removeClass('d-none');
                        })
                        .fail(function (xhr) {
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                var errorMessage = xhr.responseJSON.message;
                            } else {
                                var errorMessage = xhr.status + ': ' + xhr.statusText;
                            }
                            alert('Error - ' + errorMessage);
                        })
                        .always(function () {
                            $curtain.addClass('d-none');
                        });
                },
            });
            $form.on('submit', function (evt) {
                $resultContainer.addClass('d-none');
                evt.preventDefault ? evt.preventDefault() : (evt.returnValue = false);
            });
        });
    </script>
</body>
</html>
