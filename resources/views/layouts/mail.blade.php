<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title></title>
</head>

<body style="background-color:#fff;">

<table cellspacing="0" cellpadding="0" style="width:600px;margin:auto;margin-top:20px;margin-bottom:20px;font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;background-color:#FFF;border:none;font-size:12px !important;">
    <tr>
        <td>
            <table cellspacing="0" cellpadding="0" style="width:600px;border:none;">
                <tr><td style="height:15px;"></td></tr>
                <tr>
                    <td style="text-align:center;">
                        <a href="/" target="_blank" style="border:none;text-decoration:none;">
                            <img src="{{ asset(setting('generals.logo')) }}"
                                 alt="{{ setting('generals.websiteName') }}"
                                 style="max-width:200px;"
                            >
                        </a>
                    </td>
                </tr>
                <tr><td style="height:40px;"></td></tr>
                <tr>
                    <td>
                        <table cellspacing="0" cellpadding="0" style="width:560px;border:none;margin:0 auto 0 auto;">
                            <tr>
                                <td>
                                    @yield('message')
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

</body>
</html>
