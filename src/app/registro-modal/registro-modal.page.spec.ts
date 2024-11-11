import { ComponentFixture, TestBed } from '@angular/core/testing';
import { RegistroModalPage } from './registro-modal.page';

describe('RegistroModalPage', () => {
  let component: RegistroModalPage;
  let fixture: ComponentFixture<RegistroModalPage>;

  beforeEach(async(() => {
    fixture = TestBed.createComponent(RegistroModalPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  }));

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
