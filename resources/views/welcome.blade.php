<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CSBS Sainte Colette — Complexe Scolaire Bilingue</title>
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
*{margin:0;padding:0;box-sizing:border-box;}
html{scroll-behavior:smooth;}
body{font-family:'Nunito Sans',sans-serif;background:var(--creme);color:var(--texte);}

/* ── NAV ── */
nav{
  position:fixed;top:0;width:100%;z-index:100;
  background:var(--rouge);
  height:68px;display:flex;align-items:center;
  padding:0 5vw;gap:1.5rem;
  border-bottom:2px solid var(--or);
}
.nav-brand{display:flex;align-items:center;gap:12px;text-decoration:none;flex-shrink:0;}
.nav-logo-circle{
  width:46px;height:46px;border-radius:50%;
  border:2px solid var(--or);
  overflow:hidden;
  display:flex;align-items:center;justify-content:center;
  flex-shrink:0;
}
.nav-logo-circle img{
  width:100%;height:100%;object-fit:cover;border-radius:50%;
}
.nav-brand-text{line-height:1.2;}
.nav-brand-text strong{
  display:block;color:var(--blanc);
  font-family:'Cormorant Garamond',serif;
  font-size:13.5px;font-weight:700;letter-spacing:0.3px;
}
.nav-brand-text span{color:var(--or-clair);font-size:9px;letter-spacing:2.5px;text-transform:uppercase;}
.nav-sep{flex:1;}
.nav-links{display:flex;gap:0;list-style:none;}
.nav-links a{
  color:rgba(255,255,255,0.72);text-decoration:none;
  font-size:12px;font-weight:600;letter-spacing:0.7px;
  padding:6px 12px;border-radius:3px;transition:color 0.2s;
}
.nav-links a:hover{color:var(--blanc);}
.nav-links a.actif{color:var(--or-clair);}
.btn-connexion{
  background:var(--or)!important;color:var(--rouge)!important;
  font-weight:700!important;padding:7px 16px!important;
  border-radius:3px!important;transition:background 0.2s!important;
}
.btn-connexion:hover{background:var(--or-clair)!important;}

/* ── HERO ── */
.hero{
  padding-top:68px;min-height:100vh;
  display:grid;grid-template-columns:1fr 1fr;
  position:relative;overflow:hidden;
}
.hero-gauche{
  background:var(--rouge);
  display:flex;flex-direction:column;justify-content:center;
  padding:5rem 5vw 5rem 8vw;position:relative;
}
.hero-gauche::after{
  content:'';position:absolute;right:-40px;top:0;bottom:0;
  width:80px;background:var(--rouge);
  clip-path:polygon(0 0, 0% 100%, 100% 100%);z-index:2;
}
.deco-croix{position:absolute;top:2rem;right:3rem;opacity:0.12;}
.hero-tag{
  display:inline-flex;align-items:center;gap:8px;
  color:var(--or-clair);font-size:10px;
  letter-spacing:3px;text-transform:uppercase;
  margin-bottom:1.5rem;font-weight:600;
}
.hero-tag::before{content:'✦';font-size:8px;}
.hero-titre{
  font-family:'Cormorant Garamond',serif;
  font-size:clamp(2.6rem,4.2vw,4rem);
  font-weight:700;color:var(--blanc);
  line-height:1.08;margin-bottom:1.4rem;
}
.hero-titre em{color:var(--or-clair);font-style:normal;}
.hero-desc{color:rgba(255,255,255,0.62);font-size:15px;line-height:1.75;max-width:400px;margin-bottom:2rem;}
.hero-devise{
  font-family:'Cormorant Garamond',serif;
  font-size:1.05rem;font-style:italic;
  color:var(--or);margin-bottom:2.5rem;
  padding-left:1rem;border-left:2px solid var(--or);
}
.hero-btns{display:flex;gap:1rem;flex-wrap:wrap;}
.btn-or{
  background:var(--or);color:var(--rouge);
  padding:12px 28px;border-radius:3px;
  font-size:11px;font-weight:700;letter-spacing:1.2px;
  text-transform:uppercase;text-decoration:none;
  transition:all 0.2s;display:inline-block;
}
.btn-or:hover{background:var(--or-clair);transform:translateY(-1px);}
.btn-ghost{
  background:transparent;color:rgba(255,255,255,0.75);
  padding:12px 28px;border-radius:3px;
  font-size:11px;font-weight:600;letter-spacing:1px;
  text-transform:uppercase;text-decoration:none;
  border:1px solid rgba(255,255,255,0.3);transition:all 0.2s;
}
.btn-ghost:hover{border-color:rgba(255,255,255,0.65);color:var(--blanc);}

.hero-droite{
  background:var(--gris);
  display:flex;align-items:center;justify-content:center;
  padding:5rem 5vw;
}
.hero-stats{
  display:grid;grid-template-columns:1fr 1fr;
  gap:1.5px;background:var(--bord);
  border:1px solid var(--bord);
}
.stat-bloc{background:var(--blanc);padding:2rem 1.75rem;display:flex;flex-direction:column;gap:0.4rem;}
.stat-num{font-family:'Cormorant Garamond',serif;font-size:3rem;font-weight:700;color:var(--rouge);line-height:1;}
.stat-num span{color:var(--or);}
.stat-label{font-size:11px;color:var(--texte-doux);letter-spacing:1px;text-transform:uppercase;}
.stat-sub{font-size:12px;color:var(--orange);font-weight:600;margin-top:2px;}

/* ── SECTIONS COMMUNES ── */
section{padding:5rem 8vw;}
.s-tag{font-size:10px;letter-spacing:3px;text-transform:uppercase;color:var(--or);font-weight:700;margin-bottom:0.6rem;}
.s-titre{font-family:'Cormorant Garamond',serif;font-size:clamp(2rem,3.5vw,2.8rem);font-weight:700;line-height:1.1;margin-bottom:1rem;}
.s-desc{font-size:14.5px;color:var(--texte-doux);max-width:520px;line-height:1.8;margin-bottom:3rem;}
.ligne-or{width:40px;height:2px;background:var(--or);margin-bottom:2rem;}

/* ── RÉSULTATS ── */
#resultats{background:var(--blanc);}
.resultats-grille{display:grid;grid-template-columns:repeat(3,1fr);gap:1px;background:var(--bord);border:1px solid var(--bord);}
.resultat-bloc{background:var(--blanc);padding:2.5rem 2rem;transition:background 0.25s;}
.resultat-bloc:hover{background:var(--creme);}
.res-annee{font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--or);margin-bottom:0.5rem;font-weight:700;}
.res-examen{font-family:'Cormorant Garamond',serif;font-size:1.3rem;font-weight:600;margin-bottom:1.5rem;color:var(--texte);}
.res-taux{font-family:'Cormorant Garamond',serif;font-size:3.5rem;font-weight:700;color:var(--rouge);line-height:1;margin-bottom:0.3rem;}
.res-taux sup{font-size:1.5rem;}
.res-mention{font-size:11px;color:var(--texte-doux);letter-spacing:1px;text-transform:uppercase;margin-bottom:1.5rem;}
.barre-wrap{margin-bottom:0.75rem;}
.barre-info{display:flex;justify-content:space-between;font-size:11px;color:var(--texte-doux);margin-bottom:5px;}
.barre-fond{height:4px;background:var(--gris);}
.barre-remplir{height:100%;background:var(--orange);width:0;transition:width 1.4s cubic-bezier(0.16,1,0.3,1);}
.res-badge{display:inline-block;margin-top:1rem;font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:var(--rouge);border:1px solid var(--rouge);padding:3px 10px;}

/* graphe évolution */
.evolution-wrap{margin-top:2rem;}
.evol-graphe{display:flex;align-items:flex-end;gap:12px;height:120px;padding:0 0 0 20px;position:relative;}
.evol-graphe::before{content:'';position:absolute;left:0;top:0;bottom:0;width:1px;background:var(--bord);}
.evol-barre-wrap{display:flex;flex-direction:column;align-items:center;gap:6px;flex:1;}
.evol-barre{width:100%;background:var(--gris);position:relative;}
.evol-fill{position:absolute;bottom:0;left:0;right:0;background:var(--rouge);transition:height 1.2s ease;height:0;}
.evol-fill.or{background:var(--or);}
.evol-an{font-size:10px;color:var(--texte-doux);letter-spacing:1px;}
.evol-val{font-size:11px;color:var(--rouge);font-weight:700;}

/* ── FILIÈRES ── */
#filieres{background:var(--creme);}
.filieres-grille{display:grid;grid-template-columns:repeat(4,1fr);gap:1px;background:var(--bord);border:1px solid var(--bord);}
.filiere-bloc{background:var(--blanc);padding:2rem 1.5rem;transition:all 0.25s;cursor:default;}
.filiere-bloc:hover{background:var(--rouge);}
.filiere-bloc:hover .fil-nom,.filiere-bloc:hover .fil-matiere{color:var(--blanc);}
.filiere-bloc:hover .fil-cycle{color:var(--or-clair);}
.fil-serie{font-size:10px;letter-spacing:2.5px;text-transform:uppercase;color:var(--or);font-weight:700;margin-bottom:0.5rem;}
.fil-nom{font-family:'Cormorant Garamond',serif;font-size:1.2rem;font-weight:700;color:var(--texte);margin-bottom:0.3rem;transition:color 0.25s;}
.fil-matiere{font-size:12px;color:var(--texte-doux);transition:color 0.25s;margin-bottom:0.5rem;}
.fil-cycle{font-size:10px;letter-spacing:1px;text-transform:uppercase;color:var(--rouge);font-weight:600;transition:color 0.25s;}

/* ── VALEURS ── */
#valeurs{background:var(--rouge);}
#valeurs .s-titre{color:var(--blanc);}
#valeurs .s-desc{color:rgba(255,255,255,0.55);}
#valeurs .s-tag{color:var(--or-clair);}
.valeurs-grille{display:grid;grid-template-columns:repeat(3,1fr);gap:1.5rem;}
.valeur-card{
  background:rgba(255,255,255,0.06);
  border:1px solid rgba(255,255,255,0.12);
  padding:2.5rem 2rem;text-decoration:none;
  transition:all 0.25s;display:block;
}
.valeur-card:hover{background:rgba(255,255,255,0.1);border-color:var(--or);transform:translateY(-2px);}
.val-icon{font-family:'Cormorant Garamond',serif;font-size:2.4rem;color:var(--or);margin-bottom:1rem;line-height:1;}
.val-titre{font-family:'Cormorant Garamond',serif;font-size:1.35rem;font-weight:700;color:var(--blanc);margin-bottom:0.5rem;}
.val-desc{font-size:13px;color:rgba(255,255,255,0.5);line-height:1.65;}

/* ── ESPACE FAMILLE ── */
#parent{background:var(--creme);}
.parent-layout{display:grid;grid-template-columns:1fr 1fr;gap:5rem;align-items:center;}
.parent-features{display:flex;flex-direction:column;gap:1.5rem;}
.feat{display:flex;gap:1.25rem;align-items:flex-start;}
.feat-num{
  width:32px;height:32px;min-width:32px;
  border:1.5px solid var(--or);color:var(--or);
  display:flex;align-items:center;justify-content:center;
  font-family:'Cormorant Garamond',serif;font-size:13px;font-weight:700;
}
.feat-titre{font-size:14px;font-weight:700;margin-bottom:0.25rem;}
.feat-desc{font-size:13px;color:var(--texte-doux);line-height:1.6;}
.parent-mockup{
  background:var(--rouge);border-radius:32px;
  padding:2rem 1.5rem;max-width:280px;margin:0 auto;
  box-shadow:0 30px 80px rgba(139,26,26,0.25);
}
.mock-barre{display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;}
.mock-titre{color:var(--blanc);font-family:'Cormorant Garamond',serif;font-size:1.1rem;font-weight:700;}
.mock-sub{color:var(--or-clair);font-size:10px;letter-spacing:1px;}
.mock-eleve{background:rgba(255,255,255,0.08);border-radius:8px;padding:1rem;margin-bottom:1rem;}
.mock-eleve-nom{color:var(--blanc);font-size:13px;font-weight:700;margin-bottom:0.25rem;}
.mock-eleve-classe{color:rgba(255,255,255,0.5);font-size:11px;}
.mock-notes{display:flex;flex-direction:column;gap:0.5rem;}
.mock-note{background:rgba(255,255,255,0.06);border-radius:6px;padding:0.75rem;display:flex;justify-content:space-between;align-items:center;}
.mock-matiere{color:rgba(255,255,255,0.75);font-size:11px;}
.mock-val{color:var(--or-clair);font-family:'Cormorant Garamond',serif;font-size:1.2rem;font-weight:700;}
.mock-val span{color:rgba(255,255,255,0.35);font-size:0.65rem;}
.mock-btn{width:100%;margin-top:1rem;background:var(--or);color:var(--rouge);border:none;padding:10px;border-radius:6px;font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;cursor:default;font-family:'Nunito Sans',sans-serif;}

/* ── ACTUALITÉS ── */
#actualites{background:var(--blanc);}
.actu-grille{display:grid;grid-template-columns:2fr 1fr 1fr;gap:1.5rem;}
.actu-card{background:var(--creme);border:1px solid var(--bord);overflow:hidden;text-decoration:none;display:block;transition:border-color 0.25s;}
.actu-card:hover{border-color:var(--or);}
.actu-img{height:200px;background:var(--gris);overflow:hidden;display:flex;align-items:center;justify-content:center;}
.actu-img img{width:100%;height:100%;object-fit:cover;}
.actu-corps{padding:1.5rem;}
.actu-cat{font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--or);font-weight:700;margin-bottom:0.5rem;}
.actu-titre{font-family:'Cormorant Garamond',serif;font-size:1.15rem;font-weight:700;color:var(--texte);margin-bottom:0.5rem;line-height:1.35;}
.actu-date{font-size:11px;color:var(--texte-doux);}
.actu-card.principal .actu-img{height:260px;}
.actu-card.principal .actu-titre{font-size:1.4rem;}

/* ── CONTACT ── */
#contact{background:var(--blanc);}
.contact-layout{display:grid;grid-template-columns:1fr 1fr;gap:5rem;}
.contact-infos{display:flex;flex-direction:column;gap:2rem;}
.cinfo-bloc{border-left:2px solid var(--or);padding-left:1.25rem;}
.cinfo-label{font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--or);font-weight:700;margin-bottom:0.4rem;}
.cinfo-val{font-size:14px;color:var(--texte);line-height:1.6;}
.contact-form{display:flex;flex-direction:column;gap:1rem;}
.contact-form input,.contact-form select,.contact-form textarea{
  width:100%;padding:11px 14px;border:1px solid var(--bord);border-radius:2px;
  background:var(--creme);color:var(--texte);
  font-family:'Nunito Sans',sans-serif;font-size:13.5px;
  outline:none;transition:border-color 0.2s;
}
.contact-form input:focus,.contact-form select:focus,.contact-form textarea:focus{border-color:var(--rouge);}
.contact-form textarea{resize:vertical;min-height:110px;}

/* ── FOOTER ── */
footer{
  background:var(--rouge);
  border-top:1px solid rgba(255,255,255,0.1);
  padding:3rem 8vw;
}
.footer-haut{display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:3rem;margin-bottom:2.5rem;}
.footer-marque strong{font-family:'Cormorant Garamond',serif;font-size:1.2rem;color:var(--blanc);display:block;margin-bottom:0.5rem;}
.footer-marque p{font-size:12px;color:rgba(255,255,255,0.45);line-height:1.7;}
.footer-col-titre{font-size:10px;letter-spacing:2.5px;text-transform:uppercase;color:var(--or);font-weight:700;margin-bottom:1rem;}
.footer-col a{display:block;font-size:12.5px;color:rgba(255,255,255,0.5);text-decoration:none;margin-bottom:0.5rem;transition:color 0.2s;}
.footer-col a:hover{color:rgba(255,255,255,0.85);}
.footer-bas{border-top:1px solid rgba(255,255,255,0.1);padding-top:1.5rem;display:flex;justify-content:space-between;}
.footer-bas p{font-size:11px;color:rgba(255,255,255,0.35);}

/* ── RESPONSIVE ── */
@media(max-width:900px){
  .hero{grid-template-columns:1fr;}
  .hero-gauche::after{display:none;}
  .resultats-grille{grid-template-columns:1fr;}
  .filieres-grille{grid-template-columns:1fr 1fr;}
  .valeurs-grille{grid-template-columns:1fr;}
  .actu-grille{grid-template-columns:1fr;}
  .parent-layout,.contact-layout{grid-template-columns:1fr;}
  .footer-haut{grid-template-columns:1fr 1fr;gap:2rem;}
  .nav-links{display:none;}
}
</style>
</head>
<body>

<!-- NAV -->
<nav>
  <a href="index.html" class="nav-brand">
    <div class="nav-logo-circle">
      <img src="{{ asset('assets/images/LOGOSC.jpeg') }}" alt="Logo Sainte Colette">
    </div>
    <div class="nav-brand-text">
      <strong>Complexe Scolaire Bilingue Sainte Colette</strong>
      <span>Discipline · Créativité · Excellence</span>
    </div>
  </a>
  <div class="nav-sep"></div>
  <ul class="nav-links">
    <li><a href="index.html" class="actif">Accueil</a></li>
    <li><a href="resultats.html">Résultats</a></li>
    <li><a href="filieres.html">Filières</a></li>
    <li><a href="actualites.html">Actualités</a></li>
    <li><a href="administration.html">Administration</a></li>
    <li><a href="contact.html">Contact</a></li>
    <li><a href="/login" class="btn-connexion">Espace Famille</a></li>
  </ul>
</nav>

<!-- HERO -->
<div class="hero">
  <div class="hero-gauche">
    <div class="deco-croix">
      <svg width="80" height="80" viewBox="0 0 80 80" fill="none">
        <rect x="35" y="5" width="10" height="70" rx="4" fill="white"/>
        <rect x="10" y="28" width="60" height="10" rx="4" fill="white"/>
      </svg>
    </div>
    <span class="hero-tag">Fondé avec foi · Cotonou, Bénin</span>
    <h1 class="hero-titre">Discipline,<br>Créativité,<br><em>Excellence</em></h1>
    <p class="hero-desc">Le Complexe Scolaire Bilingue Sainte Colette est un établissement d'enseignement fondé sur les valeurs chrétiennes, l'ouverture bilingue et la rigueur académique au cœur de Cotonou.</p>
    <div class="hero-devise">"Sainte Colette — Patronne de la foi et de la ténacité"</div>
    <div class="hero-btns">
      <a href="resultats.html" class="btn-or">Nos résultats</a>
      <a href="connexion.html" class="btn-ghost">Espace Famille</a>
    </div>
  </div>
  <div class="hero-droite">
    <div class="hero-stats">
      <div class="stat-bloc">
        <div class="stat-num">94<span>%</span></div>
        <div class="stat-label">Taux CEP</div>
        <div class="stat-sub">Session 2024</div>
      </div>
      <div class="stat-bloc">
        <div class="stat-num">89<span>%</span></div>
        <div class="stat-label">Taux BEPC</div>
        <div class="stat-sub">Session 2024</div>
      </div>
      <div class="stat-bloc">
        <div class="stat-num">1<span>200</span></div>
        <div class="stat-label">Élèves</div>
        <div class="stat-sub">Année 2024–25</div>
      </div>
      <div class="stat-bloc">
        <div class="stat-num">60<span>+</span></div>
        <div class="stat-label">Enseignants</div>
        <div class="stat-sub">Corps bilingue</div>
      </div>
    </div>
  </div>
</div>

<!-- RÉSULTATS -->
<section id="resultats">
  <div class="s-tag">Performances académiques</div>
  <h2 class="s-titre">Taux de réussite aux examens</h2>
  <div class="ligne-or"></div>
  <div class="resultats-grille">

    <!-- CEP -->
    <div class="resultat-bloc">
      <div class="res-annee">Session 2024</div>
      <div class="res-examen">Certificat d'Études Primaires (CEP)</div>
      <div class="res-taux">94<sup>%</sup></div>
      <div class="res-mention">Taux de réussite</div>
      <div class="barre-wrap"><div class="barre-info"><span>Mention Très Bien</span><span>40%</span></div><div class="barre-fond"><div class="barre-remplir" data-w="40"></div></div></div>
      <div class="barre-wrap"><div class="barre-info"><span>Mention Bien</span><span>32%</span></div><div class="barre-fond"><div class="barre-remplir" data-w="32"></div></div></div>
      <div class="barre-wrap"><div class="barre-info"><span>Mention Assez Bien</span><span>22%</span></div><div class="barre-fond"><div class="barre-remplir" data-w="22"></div></div></div>
      <div class="res-badge">Meilleur résultat depuis 2019</div>
    </div>

    <!-- BEPC -->
    <div class="resultat-bloc">
      <div class="res-annee">Session 2024</div>
      <div class="res-examen">Brevet d'Études du Premier Cycle (BEPC)</div>
      <div class="res-taux">89<sup>%</sup></div>
      <div class="res-mention">Taux de réussite</div>
      <div class="barre-wrap"><div class="barre-info"><span>Mention Très Bien</span><span>26%</span></div><div class="barre-fond"><div class="barre-remplir" data-w="26"></div></div></div>
      <div class="barre-wrap"><div class="barre-info"><span>Mention Bien</span><span>38%</span></div><div class="barre-fond"><div class="barre-remplir" data-w="38"></div></div></div>
      <div class="barre-wrap"><div class="barre-info"><span>Mention Assez Bien</span><span>25%</span></div><div class="barre-fond"><div class="barre-remplir" data-w="25"></div></div></div>
      <div class="res-badge">Top Cotonou</div>
    </div>

    <!-- Évolution BEPC -->
    <div class="resultat-bloc">
      <div class="res-annee">Évolution BEPC — 5 ans</div>
      <div class="res-examen">Brevet d'Études du Premier Cycle</div>
      <div class="evolution-wrap">
        <div class="evol-graphe" id="evol">
          <div class="evol-barre-wrap"><div class="evol-barre" style="height:100px"><div class="evol-fill" data-h="74"></div></div><div class="evol-an">2020</div><div class="evol-val">74%</div></div>
          <div class="evol-barre-wrap"><div class="evol-barre" style="height:100px"><div class="evol-fill" data-h="79"></div></div><div class="evol-an">2021</div><div class="evol-val">79%</div></div>
          <div class="evol-barre-wrap"><div class="evol-barre" style="height:100px"><div class="evol-fill" data-h="82"></div></div><div class="evol-an">2022</div><div class="evol-val">82%</div></div>
          <div class="evol-barre-wrap"><div class="evol-barre" style="height:100px"><div class="evol-fill" data-h="86"></div></div><div class="evol-an">2023</div><div class="evol-val">86%</div></div>
          <div class="evol-barre-wrap"><div class="evol-barre" style="height:100px"><div class="evol-fill or" data-h="89"></div></div><div class="evol-an">2024</div><div class="evol-val">89%</div></div>
        </div>
      </div>
      <div class="res-badge" style="margin-top:1rem">+15 points en 5 ans</div>
    </div>

  </div>
</section>

<!-- FILIÈRES -->
<section id="filieres">
  <div class="s-tag">Offre pédagogique bilingue</div>
  <h2 class="s-titre">Filières & Niveaux</h2>
  <div class="ligne-or"></div>
  <div class="filieres-grille">
    <div class="filiere-bloc"><div class="fil-serie">Maternelle</div><div class="fil-nom">Petite Section</div><div class="fil-matiere">Éveil, Motricité, Langage</div><div class="fil-cycle">Cycle Maternelle</div></div>
    <div class="filiere-bloc"><div class="fil-serie">Maternelle</div><div class="fil-nom">Moyenne & Grande Section</div><div class="fil-matiere">Lecture, Calcul, Anglais initiation</div><div class="fil-cycle">Cycle Maternelle</div></div>
    <div class="filiere-bloc"><div class="fil-serie">Primaire</div><div class="fil-nom">CP — CM2</div><div class="fil-matiere">Français, Maths, Anglais, Sciences</div><div class="fil-cycle">Préparation au CEP</div></div>
    <div class="filiere-bloc"><div class="fil-serie">Primaire Bilingue</div><div class="fil-nom">Cours Bilingues FR/EN</div><div class="fil-matiere">Enseignement en Français & Anglais</div><div class="fil-cycle">Toutes classes primaires</div></div>
    <div class="filiere-bloc"><div class="fil-serie">1er Cycle</div><div class="fil-nom">Collège Bilingue</div><div class="fil-matiere">6e, 5e, 4e, 3e — FR & EN</div><div class="fil-cycle">Préparation au BEPC</div></div>
    <div class="filiere-bloc"><div class="fil-serie">1er Cycle</div><div class="fil-nom">Sciences & Maths</div><div class="fil-matiere">Mathématiques, Physique, SVT</div><div class="fil-cycle">Préparation au BEPC</div></div>
    <div class="filiere-bloc"><div class="fil-serie">Parascolaire</div><div class="fil-nom">Sport & Créativité</div><div class="fil-matiere">Football, Théâtre, Musique</div><div class="fil-cycle">Activités périscolaires</div></div>
    <div class="filiere-bloc"><div class="fil-serie">Pastorale</div><div class="fil-nom">Foi & Valeurs</div><div class="fil-matiere">Catéchèse, Aumônerie, Retraites</div><div class="fil-cycle">Vie spirituelle</div></div>
  </div>
</section>

<!-- VALEURS -->
<section id="valeurs">
  <div class="s-tag">Notre identité</div>
  <h2 class="s-titre">Trois piliers fondateurs</h2>
  <div class="ligne-or"></div>
  <div class="valeurs-grille">
    <div class="valeur-card">
      <div class="val-icon">✝</div>
      <div class="val-titre">Discipline</div>
      <div class="val-desc">Un cadre structurant où chaque élève apprend le respect, la ponctualité et la rigueur — bases indispensables pour réussir dans la vie et les études.</div>
    </div>
    <div class="valeur-card">
      <div class="val-icon">✦</div>
      <div class="val-titre">Créativité</div>
      <div class="val-desc">L'ouverture bilingue et les activités parascolaires cultivent l'expression, l'imagination et l'adaptabilité chez nos élèves dans un monde en mutation.</div>
    </div>
    <div class="valeur-card">
      <div class="val-icon">◆</div>
      <div class="val-titre">Excellence</div>
      <div class="val-desc">Des résultats au CEP et au BEPC qui parlent d'eux-mêmes. Chaque élève est accompagné pour donner le meilleur de lui-même, académiquement et humainement.</div>
    </div>
  </div>
</section>

<!-- ESPACE FAMILLE -->
<section id="parent">
  <div class="s-tag">Application mobile</div>
  <h2 class="s-titre">Espace Famille</h2>
  <div class="ligne-or"></div>
  <p class="s-desc">Les parents et tuteurs suivent en temps réel la scolarité de leurs enfants : notes, absences, bulletins et communications de l'établissement.</p>
  <div class="parent-layout">
    <div class="parent-features">
      <div class="feat"><div class="feat-num">01</div><div><div class="feat-titre">Consultation des notes</div><div class="feat-desc">Accès immédiat aux notes par matière, coefficients et moyennes générales dès leur saisie par les enseignants.</div></div></div>
      <div class="feat"><div class="feat-num">02</div><div><div class="feat-titre">Suivi des absences</div><div class="feat-desc">Notification automatique en cas d'absence ou de retard. Justification en ligne possible depuis l'application.</div></div></div>
      <div class="feat"><div class="feat-num">03</div><div><div class="feat-titre">Bulletins de notes</div><div class="feat-desc">Téléchargement des bulletins trimestriels officiels au format PDF, signés électroniquement par la direction.</div></div></div>
      <div class="feat"><div class="feat-num">04</div><div><div class="feat-titre">Messagerie école–famille</div><div class="feat-desc">Communication directe avec les enseignants et l'administration. Historique conservé et consultable à tout moment.</div></div></div>
      <div class="feat"><div class="feat-num">05</div><div><div class="feat-titre">Calendrier scolaire</div><div class="feat-desc">Emploi du temps, dates d'examens, réunions parents-professeurs et événements de l'établissement.</div></div></div>
      <a href="connexion.html" class="btn-or" style="align-self:flex-start;margin-top:0.5rem">Accéder à l'espace famille</a>
    </div>
    <div>
      <div class="parent-mockup">
        <div class="mock-barre">
          <div><div class="mock-titre">Espace Famille</div><div class="mock-sub">CSBS · Sainte Colette</div></div>
        </div>
        <div class="mock-eleve">
          <div class="mock-eleve-nom">AGOSSOU Félicité</div>
          <div class="mock-eleve-classe">3ème · Année 2024–25</div>
        </div>
        <div class="mock-notes">
          <div class="mock-note"><span class="mock-matiere">Mathématiques</span><span class="mock-val">16<span>/20</span></span></div>
          <div class="mock-note"><span class="mock-matiere">Sciences de la Vie</span><span class="mock-val">15<span>/20</span></span></div>
          <div class="mock-note"><span class="mock-matiere">Anglais</span><span class="mock-val">17<span>/20</span></span></div>
          <div class="mock-note"><span class="mock-matiere">Français</span><span class="mock-val">14<span>/20</span></span></div>
          <div class="mock-note" style="border-top:1px solid rgba(255,255,255,0.08);margin-top:4px;padding-top:12px">
            <span class="mock-matiere" style="color:var(--or-clair);font-weight:700">Moyenne générale</span>
            <span class="mock-val" style="color:var(--or-clair)">15.5<span>/20</span></span>
          </div>
        </div>
        <button class="mock-btn">Voir le bulletin complet</button>
      </div>
    </div>
  </div>
</section>

<!-- ACTUALITÉS -->
<section id="actualites">
  <div class="s-tag">Vie du complexe</div>
  <h2 class="s-titre">Actualités</h2>
  <div class="ligne-or"></div>
  <div class="actu-grille">
    <a href="actualites.html" class="actu-card principal">
      <div class="actu-img" style="background:var(--gris);display:flex;align-items:center;justify-content:center;">
        <svg width="60" height="60" viewBox="0 0 60 60" fill="none"><rect x="27" y="5" width="6" height="50" rx="3" fill="#C8922A" opacity="0.4"/><rect x="5" y="22" width="50" height="6" rx="3" fill="#C8922A" opacity="0.4"/></svg>
      </div>
      <div class="actu-corps">
        <div class="actu-cat">Résultats · BEPC 2024</div>
        <div class="actu-titre">Le CSBS Sainte Colette atteint 89% au BEPC 2024 — meilleur résultat en 6 ans</div>
        <div class="actu-date">15 août 2024</div>
      </div>
    </a>
    <a href="actualites.html" class="actu-card">
      <div class="actu-img" style="background:var(--gris);"></div>
      <div class="actu-corps">
        <div class="actu-cat">Inscription</div>
        <div class="actu-titre">Dossiers d'inscription 2024–2025 : conditions et calendrier</div>
        <div class="actu-date">1er septembre 2024</div>
      </div>
    </a>
    <a href="actualites.html" class="actu-card">
      <div class="actu-img" style="background:var(--gris);"></div>
      <div class="actu-corps">
        <div class="actu-cat">Pastorale</div>
        <div class="actu-titre">Messe de rentrée et journée Sainte Colette : programme complet</div>
        <div class="actu-date">5 octobre 2024</div>
      </div>
    </a>
  </div>
</section>

<!-- CONTACT -->
<section id="contact">
  <div class="s-tag">Nous joindre</div>
  <h2 class="s-titre">Contact</h2>
  <div class="ligne-or"></div>
  <div class="contact-layout">
    <div class="contact-infos">
      <div class="cinfo-bloc"><div class="cinfo-label">Adresse</div><div class="cinfo-val">Cotonou, République du Bénin</div></div>
      <div class="cinfo-bloc"><div class="cinfo-label">Téléphone</div><div class="cinfo-val">+229 XX XX XX XX</div></div>
      <div class="cinfo-bloc"><div class="cinfo-label">Email</div><div class="cinfo-val">contact@saintecolette-benin.edu.bj</div></div>
      <div class="cinfo-bloc"><div class="cinfo-label">Accueil</div><div class="cinfo-val">Lundi – Vendredi : 7h30 – 17h00<br>Samedi : 8h00 – 12h00</div></div>
    </div>
    <form class="contact-form" onsubmit="return false">
      <input type="text" placeholder="Nom complet">
      <input type="email" placeholder="Adresse e-mail">
      <select>
        <option value="">Objet du message</option>
        <option>Demande d'inscription</option>
        <option>Information sur les filières bilingues</option>
        <option>Espace famille</option>
        <option>Autre</option>
      </select>
      <textarea placeholder="Votre message"></textarea>
      <button type="submit" class="btn-or" style="border:none;cursor:pointer">Envoyer</button>
    </form>
  </div>
</section>

<!-- FOOTER -->
<footer>
  <div class="footer-haut">
    <div class="footer-marque">
      <strong>Complexe Scolaire Bilingue Sainte Colette</strong>
      <p>Un établissement catholique d'excellence à Cotonou, formant des générations disciplinées, créatives et tournées vers l'avenir dans un cadre bilingue et spirituel.</p>
    </div>
    <div class="footer-col">
      <div class="footer-col-titre">Navigation</div>
      <a href="index.html">Accueil</a>
      <a href="resultats.html">Résultats</a>
      <a href="filieres.html">Filières</a>
      <a href="actualites.html">Actualités</a>
      <a href="administration.html">Administration</a>
    </div>
    <div class="footer-col">
      <div class="footer-col-titre">Espaces</div>
      <a href="connexion.html">Espace Parent</a>
      <a href="connexion.html">Espace Élève</a>
      <a href="connexion.html">Espace Enseignant</a>
    </div>
    <div class="footer-col">
      <div class="footer-col-titre">Informations</div>
      <a href="contact.html">Contact & Localisation</a>
      <a href="#">Mentions légales</a>
      <a href="#">Règlement intérieur</a>
    </div>
  </div>
  <div class="footer-bas">
    <p>© 2024 Complexe Scolaire Bilingue Sainte Colette · Cotonou, Bénin</p>
    <p>Discipline · Créativité · Excellence</p>
  </div>
</footer>

<script>
const obs = new IntersectionObserver(entries => {
  entries.forEach(e => {
    if (!e.isIntersecting) return;
    e.target.querySelectorAll('.barre-remplir').forEach(b => { b.style.width = b.dataset.w + '%'; });
    e.target.querySelectorAll('.evol-fill').forEach(f => { f.style.height = f.dataset.h + 'px'; });
    obs.unobserve(e.target);
  });
}, { threshold: 0.2 });
document.querySelectorAll('#resultats, #evol').forEach(el => obs.observe(el));
</script>
</body>
</html>