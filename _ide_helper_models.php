<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string $nome
 * @property string $foto
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Livro> $livros
 * @property-read int|null $livros_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Autor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Autor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Autor query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Autor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Autor whereFoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Autor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Autor whereNome($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Autor whereUpdatedAt($value)
 */
	class Autor extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nome
 * @property string $logotipo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Livro> $livros
 * @property-read int|null $livros_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Editora newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Editora newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Editora query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Editora whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Editora whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Editora whereLogotipo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Editora whereNome($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Editora whereUpdatedAt($value)
 */
	class Editora extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $editora_id
 * @property string $ISBN
 * @property string $nome
 * @property string|null $bibliografia
 * @property string|null $imagemcapa
 * @property string $preco
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Autor> $autores
 * @property-read int|null $autores_count
 * @property-read \App\Models\Editora $editora
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Requisicao> $requisicoes
 * @property-read int|null $requisicoes_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Livro newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Livro newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Livro query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Livro whereBibliografia($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Livro whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Livro whereEditoraId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Livro whereISBN($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Livro whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Livro whereImagemcapa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Livro whereNome($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Livro wherePreco($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Livro whereUpdatedAt($value)
 */
	class Livro extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $team_id
 * @property int $user_id
 * @property string|null $role
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Membership newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Membership newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Membership query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Membership whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Membership whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Membership whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Membership whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Membership whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Membership whereUserId($value)
 */
	class Membership extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $livro_id
 * @property int $ativo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Livro $livro
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requisicao newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requisicao newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requisicao query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requisicao whereAtivo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requisicao whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requisicao whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requisicao whereLivroId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requisicao whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requisicao whereUserId($value)
 */
	class Requisicao extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property bool $personal_team
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $owner
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TeamInvitation> $teamInvitations
 * @property-read int|null $team_invitations_count
 * @property-read \App\Models\Membership|null $membership
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\TeamFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team wherePersonalTeam($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereUserId($value)
 */
	class Team extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $team_id
 * @property string $email
 * @property string|null $role
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Team $team
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation whereUpdatedAt($value)
 */
	class TeamInvitation extends \Eloquent {}
}

namespace App\Models{
/**
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Requisicao[] $requisicoes
 * @property int $id
 * @property string $name
 * @property string $email
 * @property bool $is_admin
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property string|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property int|null $current_team_id
 * @property string|null $profile_photo_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Team|null $currentTeam
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Team> $ownedTeams
 * @property-read int|null $owned_teams_count
 * @property-read string $profile_photo_url
 * @property-read int|null $requisicoes_count
 * @property-read \App\Models\Membership|null $membership
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Team> $teams
 * @property-read int|null $teams_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCurrentTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfilePhotoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

