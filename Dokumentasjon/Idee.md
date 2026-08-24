# Idee myldring

Gruppen skal utvikle en webbasert samarbeidsplattform som er utformet for å gjøre det enklere for studenter å arbeide sammen i prosjekter og emner samt dele informasjon og ressurser. Den primære målgruppen er studentgrupper på universitetsnivået som jobber sammen på prosjekter eller semesteroppgaver.

## Grupper

Studentene skal kunne opprette egne grupper som fungerer som et felles samarbeidsområde. I en fullverdig applikasjon ville gruppene normalt vært knyttet til et spesifikt emne. Siden emner ikke er et eksplisitt krav i denne oppgaven, skal gruppene i denne versjonen være globale. Det innebærer at alle brukere kan se hvilke grupper som finnes på plattformen.

Alle studenter skal kunne opprette en gruppe. For å bli medlem av en eksisterende gruppe kreves det imidlertid en invitasjon. Invitasjoner skal kunne sendes enten via en direkte lenke eller på e-post. Det skal ikke være noen ulike roller eller tilgangsnivåer innad i gruppene.

## Oppgaver og diskusjoner

For å skape en tydelig struktur for samarbeidet skal grupper kunne opprette oppgaver eller diskusjonstråder. Disse skal fungere som et forum der gruppemedlemmene kan:

* diskutere en oppgave eller et tema  
* dele og laste opp filer knyttet til oppgaven

En gruppe vil dermed i praksis fungere som et strukturert forum med ulike oppgaver og diskusjoner.

Diskusjonsfunksjonen skal gjøre det mulig for medlemmer å opprette innlegg som er synlige for alle andre medlemmer av gruppen. Andre medlemmer skal kunne svare på innleggene og legge til reaksjoner.

## Filer og revisjonshistorikk

Studenter skal kunne laste opp filer og, dersom det er relevant, knytte dem til en bestemt oppgave eller diskusjonstråd. Andre medlemmer av gruppen skal kunne kommentere på filene.

Filer skal også ha en revisjonshistorikk. Det skal derfor være mulig å laste opp en nyere versjon av en eksisterende fil, samtidig som tidligere versjoner beholdes i revisjonsloggen. Kommentarer knyttet til filen skal fortsatt være tilgjengelige når en nyere versjon lastes opp.

Tilknytningen mellom en fil og en oppgave skal være valgfri. Dersom en fil ikke er knyttet til en bestemt oppgave, skal den være tilgjengelig globalt i gruppen og plasseres i en egen ”Ressurser” mappe.

## Direktemeldinge

Direktemeldinger mellom studenter kan være en relevant funksjon for plattformen. Denne funksjonaliteten anses imidlertid ikke som nødvendig for den første versjonen av produktet, og kan vurderes videre etter at en MVP er utviklet.

## Data Modell Hierarki

- Studenthub har mange grupper.   
- En gruppe har mange oppgaver.   
- En oppgave har mange diskusjonstråder.   
- Et diskusjonstråd har mange innlegg.   
- Et innlegg kan peke til et annet innlegg, det vil si innlegget er et svar.   
- Et innlegg kan ha reaksjoner (emojis) 😀…

- En gruppe kan ha mange filer  
- En oppgave kan ha mange filer  
- En fil kan ha mange kommentarer.  
- En fil kan ha revisjoner.  
- Kommentar peker til spesifikk revisjon.
