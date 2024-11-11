import { Component, Input } from '@angular/core';
import { ModalController } from '@ionic/angular';

@Component({
  selector: 'app-assignment-summary',
  templateUrl: './assignment-summary.component.html',
  styleUrls: ['./assignment-summary.component.scss'],
})
export class AssignmentSummaryComponent {
  @Input() selectedSemana: any;
  @Input() selectedDia: string;
  @Input() selectedObra: any;
  @Input() selectedEmpleados: any[];
  @Input() authService: any;

  constructor(private modalController: ModalController) {}

  closeModal() {
    this.modalController.dismiss();
  }

  confirmAssignment() {
    this.modalController.dismiss({ confirmed: true });
  }
}
