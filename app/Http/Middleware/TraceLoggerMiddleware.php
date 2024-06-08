<?php

namespace app\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpenTelemetry\API\Trace as OpenTelemetry;

class TraceLoggerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Proceed with request
        $response = $next($request);

        // Log trace information
//        $span = OpenTelemetry\TracerProvider::getDefaultTracer()->getCurrentSpan();
//        if ($span->getContext()->isValid()) {
//            Log::info('Trace ID', ['trace_id' => $span->getContext()->getTraceId()]);
//            // Add more logs as needed
//        }

        return $response;
    }
}
