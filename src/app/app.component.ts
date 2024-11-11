import { Component, OnDestroy } from '@angular/core';
import { Platform } from '@ionic/angular';
import { AccountService } from './account.service';
import { interval, Subscription } from 'rxjs';
import { catchError, retry } from 'rxjs/operators';
import { of } from 'rxjs';

@Component({
  selector: 'app-root',
  templateUrl: 'app.component.html',
  styleUrls: ['app.component.scss'],
})
export class AppComponent implements OnDestroy {
  private subscriptions: Subscription[] = [];

  constructor(
    private platform: Platform,
    private accountService: AccountService
  ) {
    this.initializeApp();
  }

  initializeApp() {
    this.platform.ready().then(() => {
      console.log('Aplicación inicializada');

      // this.startPeriodicTasks();
    });
  }

  startPeriodicTasks() {
    // Intervalo para eliminar cuentas no confirmadas
    this.subscriptions.push(
      interval(60000).subscribe(() => { // Cada 60 segundos
        console.log('Comprobando cuentas no confirmadas...');
        this.eliminarCuentasNoConfirmadas();
      })
    );

    // Intervalo para verificar estados de cuentas
    this.subscriptions.push(
      interval(60000).subscribe(() => { // Cada 60 segundos
        console.log('Comprobando estados de las cuentas...');
        this.verificarCuentasPeriodos();
      })
    );

    // Intervalo para verificar códigos de usuarios
    this.subscriptions.push(
      interval(60000).subscribe(() => { // Cada 60 segundos
        console.log('Comprobando códigos de usuarios...');
        this.verificarCodigosUsuarios();
      })
    );
  }

  eliminarCuentasNoConfirmadas() {
    this.accountService.eliminarCuentasNoConfirmadas().pipe(
      retry(3), // Reintenta la solicitud hasta 3 veces en caso de error
      catchError(error => {
        console.error('Error al eliminar cuentas:', error);
        return of(null); // Retorna un observable vacío en caso de error
      })
    ).subscribe(response => {
      if (response) {
        console.log('Cuentas eliminadas correctamente');
      }
    });
  }

  verificarCuentasPeriodos() {
    this.accountService.verificarCuentasPeriodos().pipe(
      retry(3),
      catchError(error => {
        console.error('Error al comprobar cuentas:', error);
        return of(null);
      })
    ).subscribe(response => {
      if (response) {
        console.log('Comprobación realizada correctamente');
      }
    });
  }

  verificarCodigosUsuarios() {
    this.accountService.verificarCodigosUsuarios().pipe(
      retry(3),
      catchError(error => {
        console.error('Error al comprobar códigos:', error);
        return of(null);
      })
    ).subscribe(response => {
      if (response) {
        console.log('Comprobación realizada correctamente');
      }
    });
  }

  ngOnDestroy() {
    this.subscriptions.forEach(subscription => subscription.unsubscribe());
  }
}
