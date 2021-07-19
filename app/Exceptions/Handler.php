<?php

namespace App\Exceptions;

use App\Models\User;
use App\Notifications\UserNotify;
use App\Tools\ApiCode;
use App\Tools\CacheSet;
use App\Tools\Formater;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Redis;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Qian\DingTalk\DingTalk;
use Qian\DingTalk\Message;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
        InvalidRequestException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            $appEnv = config('app.env');
            if ($appEnv == 'production' || $appEnv == 'test') {
                $usernames = ['lichun1', 'duanwenjie', 'jiangshilin'];
                $users = User::query()->whereIn('username', $usernames)->get();
                $message = new Message();
                (new DingTalk())->send(
                    config('dingtalk.token'),
                    $message->text(Formater::formatDingTalkMsg('系统异常', '', $e))
                );
                Redis::throttle(CacheSet::SYSTEM_ERROR_NOTIFY.md5($e->getMessage()))
                    ->block(0)
                    ->allow(1)->every(5 * 60)
                    ->then(function () use ($users, $e) {
                        Notification::send($users, new UserNotify(Formater::formatDingTalkMsg('系统异常', '', $e, true)));
                    }, function () {
                        // 触发并发访问上限
                    });
            }
        });
    }

    public function render($request, Throwable $e)
    {
        if (!($e instanceof InvalidRequestException)
            && !($e instanceof InternalException)
            && !($e instanceof ValidationException)
            && !($e instanceof UnauthorizedException)
            && !($e instanceof AuthenticationException)
            && !($e instanceof ThrottleRequestsException)
            && !($e instanceof MethodNotAllowedHttpException)
        ) {
            //如果是非正式环境直接暴露出问题
            if (config('app.env') != 'production') {
                return parent::render($request, $e);
            }
            return response()->json([
                'state' => ApiCode::HTTP_INTERNAL_SERVER_ERROR,
                'msg'   => ApiCode::$codeList[ApiCode::HTTP_INTERNAL_SERVER_ERROR],
                'trace' => $e->getMessage().'-----'.$e->getTraceAsString()
            ]);
        } else {
            //表单验证错误
            if ($e instanceof ValidationException) {
                return response()->json([
                    'state' => ApiCode::HTTP_UNPROCESSABLE_ENTITY,
                    'msg'   => $this->renderErrorMessage($e->errors()),
                    'data'  => $e->errors()
                ]);
            }
            //权限
            if ($e instanceof UnauthorizedException) {
                return response()->json([
                    'state' => ApiCode::HTTP_FORBIDDEN,
                    'msg'   => ApiCode::$codeList[ApiCode::HTTP_FORBIDDEN],
                ]);
            }
            //授权错误
            if ($e instanceof AuthenticationException) {
                return response()->json([
                    'state' => ApiCode::HTTP_UNAUTHORIZED,
                    'msg'   => ApiCode::$codeList[ApiCode::HTTP_UNAUTHORIZED],
                ]);
            }
            //访问频次限制
            if ($e instanceof ThrottleRequestsException) {
                return response()->json([
                    'state' => ApiCode::HTTP_TOO_MANY_REQUESTS,
                    'msg'   => ApiCode::$codeList[ApiCode::HTTP_TOO_MANY_REQUESTS],
                ]);
            }
            //请求方式方法不允许 get post 等
            if ($e instanceof MethodNotAllowedHttpException) {
                return response()->json([
                    'state' => ApiCode::HTTP_METHOD_NOT_ALLOWED,
                    'msg'   => ApiCode::$codeList[ApiCode::HTTP_METHOD_NOT_ALLOWED],
                ]);
            }
            return parent::render($request, $e);
        }
    }

    private function renderErrorMessage($errors)
    {
        $message = '';
        $uniqueErrors = [];
        foreach ($errors as $error) {
            $uniqueErrors [] = $error[0];
        }
        $uniqueErrors = array_unique($uniqueErrors);
        foreach ($uniqueErrors as $uniqueError) {
            $message .= $uniqueError.'| ';
        }
        return trim($message, '| ');
    }
}
