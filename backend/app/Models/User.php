<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasProfilePhoto;
    use Notifiable;
    use SoftDeletes;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'camara_id',
        'role_id',
        'name',
        'email',
        'password',
        'ativo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'ativo' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function permissoes(): BelongsToMany
    {
        return $this->belongsToMany(Permissao::class, 'permissao_user', 'user_id', 'permissao_id')
            ->withTimestamps();
    }

    public function hasRole(string $codigo): bool
    {
        return $this->role?->codigo === $codigo;
    }

    public function isRoot(): bool
    {
        return $this->hasRole('root');
    }

    public function hasPermission(string $codigo): bool
    {
        $this->loadMissing('permissoes');

        return $this->permissoes
            ->contains('codigo', $codigo);
    }

    public function camara(): BelongsTo
    {
        return $this->belongsTo(Camara::class);
    }

    public function vereador(): HasOne
    {
        return $this->hasOne(Vereador::class);
    }

    public function proposicoesCriadas(): HasMany
    {
        return $this->hasMany(Proposicao::class, 'criado_por_id');
    }

    public function proposicoesProtocoladas(): HasMany
    {
        return $this->hasMany(Proposicao::class, 'protocolado_por_id');
    }

    public function tramitacoesEncaminhadas(): HasMany
    {
        return $this->hasMany(Tramitacao::class, 'encaminhado_por_id');
    }

    public function tramitacoesRecebidas(): HasMany
    {
        return $this->hasMany(Tramitacao::class, 'recebido_por_id');
    }

    public function unidadesTramitacao(): BelongsToMany
    {
        return $this->belongsToMany(UnidadeTramitacao::class, 'unidade_tramitacao_user', 'user_id', 'unidade_tramitacao_id')
            ->withTimestamps();
    }

    public function sessoesCriadas(): HasMany
    {
        return $this->hasMany(Sessao::class, 'criado_por_id');
    }

    public function eventosSessaoExecutados(): HasMany
    {
        return $this->hasMany(SessaoEvento::class, 'executado_por_id');
    }

    public function itensPautaIncluidos(): HasMany
    {
        return $this->hasMany(ItemPauta::class, 'incluido_por_id');
    }

    public function presencasRegistradas(): HasMany
    {
        return $this->hasMany(SessaoPresenca::class, 'registrado_por_id');
    }

    public function presencasAtualizadas(): HasMany
    {
        return $this->hasMany(SessaoPresenca::class, 'atualizado_por_id');
    }

    public function votacoesIniciadas(): HasMany
    {
        return $this->hasMany(Votacao::class, 'aberta_por_id');
    }

    public function votacoesEncerradas(): HasMany
    {
        return $this->hasMany(Votacao::class, 'encerrada_por_id');
    }

    public function votacoesCanceladas(): HasMany
    {
        return $this->hasMany(Votacao::class, 'cancelada_por_id');
    }

    public function votosRegistrados(): HasMany
    {
        return $this->hasMany(Voto::class, 'registrado_por_id');
    }

    public function votosAtualizados(): HasMany
    {
        return $this->hasMany(Voto::class, 'atualizado_por_id');
    }
}
