import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { UploadLogoPageRoutingModule } from './upload-logo-routing.module';

import { UploadLogoPage } from './upload-logo.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    UploadLogoPageRoutingModule
  ],
  declarations: [UploadLogoPage]
})
export class UploadLogoPageModule {}
