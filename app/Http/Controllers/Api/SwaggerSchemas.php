
/**
 * @OA\Schema(
 *   schema="User",
 *   @OA\Property(property="id", type="integer"),
 *   @OA\Property(property="name", type="string"),
 *   @OA\Property(property="email", type="string")
 * )
 *
 * @OA\Schema(
 *   schema="ApiKey",
 *   @OA\Property(property="id", type="integer"),
 *   @OA\Property(property="name", type="string"),
 *   @OA\Property(property="key", type="string"),
 *   @OA\Property(property="scopes", type="array", @OA\Items(type="string"))
 * )
 */
