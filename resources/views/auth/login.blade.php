<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — CSBS Sainte Colette</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=Nunito+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --rouge:      #8B1A1A;
            --rouge-clair:#B83232;
            --orange:     #D45A1A;
            --or:         #C8922A;
            --or-clair:   #E0AA44;
            --blanc:      #ffffff;
            --creme:      #faf7f2;
            --gris:       #f0ece4;
            --texte:      #1a1510;
            --texte-doux: #6b6055;
            --bord:       #ddd5c8;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Nunito Sans', sans-serif;
            background: var(--creme);
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        /* ── GAUCHE ── */
        .gauche {
            background: var(--rouge);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 4rem 5vw;
            position: relative;
            overflow: hidden;
            border-bottom: 2px solid var(--or);
        }
        .gauche::before {
            content: '';
            position: absolute;
            right: -60px;
            top: 50%;
            transform: translateY(-50%);
            width: 120px;
            height: 120%;
            background: var(--rouge);
            clip-path: polygon(0 0, 0 100%, 100% 50%);
        }

        /* croix décorative */
        .deco-croix {
            position: absolute;
            bottom: 3rem;
            right: 4rem;
            opacity: 0.08;
        }

        .gauche-logo {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 4rem;
        }
        .logo-cercle {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid var(--or);
            flex-shrink: 0;
        }
        .logo-cercle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
        .logo-txt strong {
            display: block;
            color: var(--blanc);
            font-family: 'Cormorant Garamond', serif;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.3px;
        }
        .logo-txt span {
            color: var(--or-clair);
            font-size: 9px;
            letter-spacing: 2.5px;
            text-transform: uppercase;
        }

        .gauche-titre {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(2.2rem, 3.5vw, 3rem);
            font-weight: 700;
            color: var(--blanc);
            line-height: 1.1;
            margin-bottom: 1rem;
        }
        .gauche-titre em {
            color: var(--or-clair);
            font-style: normal;
        }
        .gauche-desc {
            color: rgba(255,255,255,0.5);
            font-size: 14px;
            line-height: 1.7;
            max-width: 380px;
            margin-bottom: 3rem;
        }

        .roles {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        .role-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.85rem 1.25rem;
            border: 1px solid rgba(255,255,255,0.08);
            cursor: pointer;
            transition: all 0.2s;
        }
        .role-item.actif,
        .role-item:hover {
            background: rgba(255,255,255,0.07);
            border-color: var(--or);
        }
        .role-num {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.2rem;
            color: var(--or);
            width: 24px;
        }
        .role-info strong {
            display: block;
            color: var(--blanc);
            font-size: 13px;
            font-weight: 600;
        }
        .role-info span {
            color: rgba(255,255,255,0.4);
            font-size: 11px;
        }

        /* ── DROITE ── */
        .droite {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4rem 5vw;
            background: var(--creme);
        }
        .connexion-box {
            width: 100%;
            max-width: 420px;
        }

        /* filet doré en haut du formulaire */
        .conn-filet {
            width: 36px;
            height: 2px;
            background: var(--or);
            margin-bottom: 1.5rem;
        }
        .conn-titre {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2rem;
            font-weight: 700;
            color: var(--texte);
            margin-bottom: 0.4rem;
        }
        .conn-sous {
            font-size: 13px;
            color: var(--texte-doux);
            margin-bottom: 2.5rem;
        }

        .onglets {
            display: flex;
            border-bottom: 1px solid var(--bord);
            margin-bottom: 2rem;
        }
        .onglet {
            padding: 0.6rem 1.25rem;
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: var(--texte-doux);
            cursor: pointer;
            border-bottom: 2px solid transparent;
            margin-bottom: -1px;
            transition: all 0.2s;
        }
        .onglet.actif {
            color: var(--rouge);
            border-bottom-color: var(--rouge);
        }
        .onglet:hover:not(.actif) {
            color: var(--texte);
        }

        .champ {
            margin-bottom: 1.1rem;
        }
        .champ label {
            display: block;
            font-size: 10.5px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            font-weight: 700;
            color: var(--texte-doux);
            margin-bottom: 0.5rem;
        }
        .champ input {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid var(--bord);
            background: var(--blanc);
            border-radius: 2px;
            font-family: 'Nunito Sans', sans-serif;
            font-size: 14px;
            color: var(--texte);
            outline: none;
            transition: border-color 0.2s;
        }
        .champ input:focus {
            border-color: var(--rouge);
        }
        .champ input.is-invalid {
            border-color: #dc3545;
        }
        .invalid-feedback {
            color: #dc3545;
            font-size: 11px;
            margin-top: 5px;
        }
        .oublie {
            font-size: 12px;
            color: var(--texte-doux);
            text-decoration: none;
            display: block;
            text-align: right;
            margin-bottom: 1.5rem;
            transition: color 0.2s;
        }
        .oublie:hover { color: var(--rouge); }

        .btn-login {
            width: 100%;
            padding: 13px;
            background: var(--rouge);
            color: var(--blanc);
            border: none;
            border-radius: 2px;
            font-family: 'Nunito Sans', sans-serif;
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-login:hover { background: var(--rouge-clair); }

        .conn-aide {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--bord);
            font-size: 12px;
            color: var(--texte-doux);
            line-height: 1.6;
        }
        .conn-aide a {
            color: var(--rouge);
            text-decoration: none;
            font-weight: 700;
        }
        .conn-aide a:hover { color: var(--orange); }

        .retour {
            display: inline-block;
            font-size: 12px;
            color: var(--texte-doux);
            text-decoration: none;
            margin-top: 1.75rem;
            letter-spacing: 0.5px;
            transition: color 0.2s;
        }
        .retour:hover { color: var(--texte); }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            padding: 10px 14px;
            margin-bottom: 1rem;
            border-radius: 2px;
            font-size: 12px;
            border-left: 3px solid #dc3545;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            body { grid-template-columns: 1fr; }
            .gauche { display: none; }
            .droite { padding: 3rem 1.5rem; }
        }
    </style>
</head>
<body>

    <!-- GAUCHE -->
    <div class="gauche">

        <!-- Croix décorative de fond -->
        <div class="deco-croix">
            <svg width="120" height="120" viewBox="0 0 120 120" fill="none">
                <rect x="52" y="5" width="16" height="110" rx="6" fill="white"/>
                <rect x="10" y="44" width="100" height="16" rx="6" fill="white"/>
            </svg>
        </div>

        <div class="gauche-logo">
            <div class="logo-cercle">
                <img src="{{ asset('assets/images/LOGOSC.jpeg') }}" alt="Sainte Colette">
            </div>
            <div class="logo-txt">
                <strong>Complexe Scolaire Bilingue Sainte Colette</strong>
                <span>Discipline · Créativité · Excellence</span>
            </div>
        </div>

        <h1 class="gauche-titre">Votre <em>espace</em><br>personnalisé</h1>
        <p class="gauche-desc">Connectez-vous pour accéder aux notes, bulletins, absences et à toutes les communications de l'établissement.</p>

        <div class="roles">
            <div class="role-item actif" data-role="parent">
                <span class="role-num">01</span>
                <div class="role-info">
                    <strong>Parent / Tuteur</strong>
                    <span>Suivi de la scolarité de vos enfants</span>
                </div>
            </div>
            <div class="role-item" data-role="enseignant">
                <span class="role-num">02</span>
                <div class="role-info">
                    <strong>Enseignant</strong>
                    <span>Saisie des notes et gestion des classes</span>
                </div>
            </div>
            <div class="role-item" data-role="admin">
                <span class="role-num">03</span>
                <div class="role-info">
                    <strong>Administrateur</strong>
                    <span>Direction et gestion de l'établissement</span>
                </div>
            </div>
        </div>
    </div>

    <!-- DROITE -->
    <div class="droite">
        <div class="connexion-box">

            <div class="conn-filet"></div>
            <div class="conn-titre">Connexion</div>
            <div class="conn-sous">Saisissez vos identifiants fournis par l'établissement.</div>

            <div class="onglets">
                <div class="onglet actif" data-role="parent">Parent</div>
                <div class="onglet" data-role="enseignant">Enseignant</div>
                <div class="onglet" data-role="admin">Administrateur</div>
            </div>

            {{-- Affichage des erreurs de session --}}
            @if(session('error'))
                <div class="alert-danger">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="champ">
                    <label for="login" id="login-label">Email ou téléphone</label>
                    <input type="text" name="login" id="login"
                           class="@error('login') is-invalid @enderror"
                           placeholder="ex. parent@csbs.edu.bj ou 61234567"
                           value="{{ old('login') }}"
                           required autofocus>
                    @error('login')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="champ">
                    <label for="password">Mot de passe</label>
                    <input type="password" name="password" id="password"
                           class="@error('password') is-invalid @enderror"
                           placeholder="••••••••"
                           required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="oublie">Mot de passe oublié ?</a>
                @endif

                <button type="submit" class="btn-login">Se connecter</button>
            </form>

            <div class="conn-aide">
                Première connexion ou identifiants perdus ?
                <a href="{{ url('/contact') }}">Contactez le secrétariat</a> de l'établissement.
            </div>

            <a href="{{ url('/index') }}" class="retour">← Retour au site de l'école</a>
        </div>
    </div>

    <script>
        (function () {
            const onglets  = document.querySelectorAll('.onglet');
            const roleItems = document.querySelectorAll('.role-item');
            const loginInput = document.getElementById('login');
            const loginLabel = document.getElementById('login-label');

            const roleConfig = {
                parent:     { label: 'Email ou téléphone (parent)',      placeholder: 'ex. parent@csbs.edu.bj ou 61234567' },
                enseignant: { label: 'Email ou téléphone (enseignant)',   placeholder: 'ex. professeur@csbs.edu.bj ou 61234567' },
                admin:      { label: 'Email ou téléphone (admin)',        placeholder: 'ex. direction@csbs.edu.bj ou 61234567' }
            };

            function setActiveRole(role) {
                onglets.forEach(o  => o.classList.toggle('actif', o.dataset.role === role));
                roleItems.forEach(r => r.classList.toggle('actif', r.dataset.role === role));
                if (roleConfig[role]) {
                    loginLabel.textContent   = roleConfig[role].label;
                    loginInput.placeholder   = roleConfig[role].placeholder;
                }
            }

            onglets.forEach(o  => o.addEventListener('click',  () => setActiveRole(o.dataset.role)));
            roleItems.forEach(r => r.addEventListener('click', () => setActiveRole(r.dataset.role)));

            const defaultRole = document.querySelector('.onglet.actif')?.dataset.role || 'parent';
            setActiveRole(defaultRole);
        })();
    </script>
</body>
</html>