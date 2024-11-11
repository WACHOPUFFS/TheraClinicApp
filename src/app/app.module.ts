import { NgModule, CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { RouteReuseStrategy } from '@angular/router';
import { HttpClientModule } from '@angular/common/http';
import { IonicModule, IonicRouteStrategy } from '@ionic/angular';
import { AppComponent } from './app.component';
import { AppRoutingModule } from './app-routing.module';
import { ModalSuccessComponent } from './modal-success/modal-success.component';
import { RechazoModalComponent } from './rechazo-modal/rechazo-modal.component';
import { FormsModule } from '@angular/forms';
import { AssignmentSummaryComponent } from './assignment-summary/assignment-summary.component';
import { EmployeeDetailsComponent } from './employee-details/employee-details.component';

// Importar el módulo de Auth0
import { AuthModule } from '@auth0/auth0-angular';

@NgModule({
  declarations: [
    AppComponent,
    ModalSuccessComponent,
    RechazoModalComponent,
    AssignmentSummaryComponent,
    EmployeeDetailsComponent
  ],
  imports: [
    BrowserModule,
    IonicModule.forRoot(),
    AppRoutingModule,
    HttpClientModule,
    FormsModule,
    // Configuración de Auth0
    AuthModule.forRoot({
      domain: 'dev-57jwrdqvo2qxlo0u.us.auth0.com',
      clientId: 'WmUOTlu2Z2i2lkGHxZmWSvrarNv5h9hE',
      authorizationParams: {
        redirect_uri: window.location.origin // o una URL explícita
      }
    })
  ],
  providers: [
    { provide: RouteReuseStrategy, useClass: IonicRouteStrategy }
  ],
  schemas: [CUSTOM_ELEMENTS_SCHEMA],
  bootstrap: [AppComponent]
})
export class AppModule {}
