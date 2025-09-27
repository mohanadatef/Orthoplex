<?php

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *   title="Orthoplex API",
 *   version="1.0.0",
 *   description="Backend Challenge API"
 * )
 * @OA\Server(url="/api")
 * @OA\SecurityScheme(
 *   securityScheme="bearerAuth",
 *   type="http",
 *   scheme="bearer",
 *   bearerFormat="JWT"
 * )
 */
