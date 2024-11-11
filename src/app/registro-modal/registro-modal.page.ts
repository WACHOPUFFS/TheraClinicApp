import { Component, Input } from '@angular/core';
import { ModalController } from '@ionic/angular';
import { DataStorageService } from '../data-storage-service';

@Component({
  selector: 'app-registro-modal',
  templateUrl: './registro-modal.page.html',
  styleUrls: ['./registro-modal.page.scss'],
})
export class RegistroModalPage {

  labelRegistarOtroUsuario: string;
  labelSi: string;
  labelNoHome: string; 

  @Input() continuarRegistro: boolean;

  constructor(private modalController: ModalController, private dataStorageService: DataStorageService) { 
    this.labelRegistarOtroUsuario = dataStorageService.labelRegistarOtroUsuario;
    this.labelSi = dataStorageService.labelSi;
    this.labelNoHome = dataStorageService.labelNoHome;
  }

  cerrarModal(continuar: boolean) {
    this.modalController.dismiss({ continuarRegistro: continuar });
  }
}
