import { ComponentFixture, TestBed } from '@angular/core/testing';
import { ProjectControlPage } from './project-control.page';

describe('ProjectControlPage', () => {
  let component: ProjectControlPage;
  let fixture: ComponentFixture<ProjectControlPage>;

  beforeEach(async(() => {
    fixture = TestBed.createComponent(ProjectControlPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  }));

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
