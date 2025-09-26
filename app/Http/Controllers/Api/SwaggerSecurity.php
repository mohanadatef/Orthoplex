
/**
 * @OA\OpenApi(
 *   @OA\Info(title="Laravel API", version="1.0.0"),
 *   @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 *   ),
 *   @OA\SecurityScheme(
 *     securityScheme="apiKeyAuth",
 *     type="apiKey",
 *     in="header",
 *     name="X-API-KEY"
 *   )
 * )
 */
