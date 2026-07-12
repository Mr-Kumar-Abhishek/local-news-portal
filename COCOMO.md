# COCOMO Calculation for Hind Bihar

This document details the Constructive Cost Model (COCOMO) calculation for the Hind Bihar local news portal project. The estimation is based on the source code lines developed for the application, taking into account the MVC architecture provided by the CodeIgniter framework.

## Source Code Statistics

Based on the analysis of the `app`, `public`, and `tests` directories (excluding vendor and framework core files), the total number of Lines of Code (LOC) is approximately:

- **Total Lines of Code (LOC):** 23,061
- **Kilo Lines of Code (KLOC):** 23.06

## Estimation Models & Cost Calculation

The Basic COCOMO model provides estimations for three different types of projects: **Organic**, **Semi-detached**, and **Embedded**. 

To calculate the estimated cost in Indian Rupees (INR), we are assuming an average developer salary of **₹1,00,000 per month**. 
*(Cost = Effort × Average Salary per Person-Month)*

### 1. Organic Mode
Used for relatively small, simple software projects where small teams with good experience work with less rigid requirements.

- **Constants:** `a = 2.4`, `b = 1.05`, `c = 2.5`, `d = 0.38`
- **Effort (E):** `2.4 * (23.06)^1.05` = **64.75 Person-Months**
- **Development Time (D):** `2.5 * (64.75)^0.38` = **12.20 Months**
- **Average Staffing:** `64.75 / 12.20` = **~5 Persons**
- **Estimated Cost (INR):** `64.75 * ₹1,00,000` = **₹64,75,000** (Sixty-four lakhs seventy-five thousand rupees)

### 2. Semi-Detached Mode
Used for medium-sized projects with mixed teams (some experienced, some inexperienced) and moderately rigid requirements.

- **Constants:** `a = 3.0`, `b = 1.12`, `c = 2.5`, `d = 0.35`
- **Effort (E):** `3.0 * (23.06)^1.12` = **100.82 Person-Months**
- **Development Time (D):** `2.5 * (100.82)^0.35` = **12.57 Months**
- **Average Staffing:** `100.82 / 12.57` = **~8 Persons**
- **Estimated Cost (INR):** `100.82 * ₹1,00,000` = **₹1,00,82,000** (One crore eighty-two thousand rupees)

### 3. Embedded Mode
Used for complex projects with highly rigid constraints and requirements (hardware, software, operational). 

- **Constants:** `a = 3.6`, `b = 1.20`, `c = 2.5`, `d = 0.32`
- **Effort (E):** `3.6 * (23.06)^1.20` = **155.51 Person-Months**
- **Development Time (D):** `2.5 * (155.51)^0.32` = **12.57 Months**
- **Average Staffing:** `155.51 / 12.57` = **~12 Persons**
- **Estimated Cost (INR):** `155.51 * ₹1,00,000` = **₹1,55,51,000** (One crore fifty-five lakhs fifty-one thousand rupees)

## Summary Table

| Project Type | Estimated Effort | Dev. Time | Optimal Team Size | Estimated Cost (INR)* |
| :--- | :--- | :--- | :--- | :--- |
| **Organic** | 64.75 Person-Months | 12.20 Months | ~5 Developers | **₹64,75,000** |
| **Semi-Detached** | 100.82 Person-Months | 12.57 Months | ~8 Developers | **₹1,00,82,000** |
| **Embedded** | 155.51 Person-Months | 12.57 Months | ~12 Developers | **₹1,55,51,000** |

*\*Assumes an average developer cost of ₹1,00,000 per month.*

## Agile Implementation Cost Breakdown

Based on the `AGILE_PLAN.md`, the project consists of 6 Sprints totaling **237 Story Points (SP)**. Since the current codebase reflects the full implementation of these Sprints, the total estimated costs above map perfectly to these 237 points. 

Below is the sprint-by-sprint cumulative cost breakdown for the **Organic Mode** (Cost per SP: ~₹27,321):

| Sprint | Story Points | Sprint Cost | Cumulative Cost |
| :--- | :--- | :--- | :--- |
| **Sprint 1** | 35 SP | ₹9,56,235 | **₹9,56,235** |
| **Sprint 2** | 39 SP | ₹1,065,519 | **₹20,21,754** |
| **Sprint 3** | 41 SP | ₹1,120,161 | **₹31,41,915** |
| **Sprint 4** | 40 SP | ₹1,092,840 | **₹42,34,755** |
| **Sprint 5** | 41 SP | ₹1,120,161 | **₹53,54,916** |
| **Sprint 6** | 41 SP | ₹1,120,161 | **₹64,75,077** |

*(Note: The total cumulative cost rounds slightly to ₹64,75,077, closely matching the estimated ₹64,75,000 Organic cost. If calculated using Semi-Detached or Embedded mode, the proportional cost per sprint scales up accordingly relative to the totals above).*
