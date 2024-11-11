// account.service.ts

import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';

@Injectable({
  providedIn: 'root'
})
export class AccountService {

  constructor(private http: HttpClient) { }

  verificarCuentasPeriodos() {
    return this.http.get('https://siinad.mx/php/verificar_cuentas_periodoss.php');
  }

  eliminarCuentasNoConfirmadas() {
    return this.http.get('https://siinad.mx/php/eliminar_cuentas_no_confirmadass.php');
  }

  verificarCodigosUsuarios() {
    return this.http.get('https://siinad.mx/php/verificar_cuentas_codigos.php');
  }
}
