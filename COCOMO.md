# COCOMO Calculation for Hind Bihar

This document details the Constructive Cost Model (COCOMO) calculation for the Hind Bihar local news portal project. The estimation is based on the source code lines developed for the application, taking into account the MVC architecture provided by the CodeIgniter framework.

## Source Code Statistics

Based on the analysis of the `app`, `public`, and `tests` directories (excluding vendor and framework core files), the total number of Lines of Code (LOC) is approximately:

- **Total Lines of Code (LOC):** 19,510
- **Kilo Lines of Code (KLOC):** 19.51

## Estimation Models & Cost Calculation

The Basic COCOMO model provides estimations for three different types of projects: **Organic**, **Semi-detached**, and **Embedded**. 

To calculate the estimated cost in Indian Rupees (INR), we are assuming an average developer salary of **₹1,00,000 per month**. 
*(Cost = Effort × Average Salary per Person-Month)*

### 1. Organic Mode
Used for relatively small, simple software projects where small teams with good experience work with less rigid requirements.

- **Constants:** `a = 2.4`, `b = 1.05`, `c = 2.5`, `d = 0.38`
- **Effort (E):** `2.4 * (19.51)^1.05` = **54.32 Person-Months**
- **Development Time (D):** `2.5 * (54.32)^0.38` = **11.41 Months**
- **Average Staffing:** `54.32 / 11.41` = **~5 Persons**
- **Estimated Cost (INR):** `54.32 * ₹1,00,000` = **₹54,32,000** (Fifty-four lakhs thirty-two thousand rupees)

### 2. Semi-Detached Mode
Used for medium-sized projects with mixed teams (some experienced, some inexperienced) and moderately rigid requirements.

- **Constants:** `a = 3.0`, `b = 1.12`, `c = 2.5`, `d = 0.35`
- **Effort (E):** `3.0 * (19.51)^1.12` = **83.60 Person-Months**
- **Development Time (D):** `2.5 * (83.60)^0.35` = **11.77 Months**
- **Average Staffing:** `83.60 / 11.77` = **~7 Persons**
- **Estimated Cost (INR):** `83.60 * ₹1,00,000` = **₹83,60,000** (Eighty-three lakhs sixty thousand rupees)

### 3. Embedded Mode
Used for complex projects with highly rigid constraints and requirements (hardware, software, operational). 

- **Constants:** `a = 3.6`, `b = 1.20`, `c = 2.5`, `d = 0.32`
- **Effort (E):** `3.6 * (19.51)^1.20` = **127.24 Person-Months**
- **Development Time (D):** `2.5 * (127.24)^0.32` = **11.79 Months**
- **Average Staffing:** `127.24 / 11.79` = **~11 Persons**
- **Estimated Cost (INR):** `127.24 * ₹1,00,000` = **₹1,27,24,000** (One crore twenty-seven lakhs twenty-four thousand rupees)

## Summary Table

| Project Type | Estimated Effort | Dev. Time | Optimal Team Size | Estimated Cost (INR)* |
| :--- | :--- | :--- | :--- | :--- |
| **Organic** | 54.32 Person-Months | 11.41 Months | ~5 Developers | **₹54,32,000** |
| **Semi-Detached** | 83.60 Person-Months | 11.77 Months | ~7 Developers | **₹83,60,000** |
| **Embedded** | 127.24 Person-Months | 11.79 Months | ~11 Developers | **₹1,27,24,000** |

*\*Assumes an average developer cost of ₹1,00,000 per month.*

## Agile Implementation Cost Breakdown

Based on the `AGILE_PLAN.md`, the project consists of 6 Sprints totaling **237 Story Points (SP)**. Since the current codebase reflects the full implementation of these Sprints, the total estimated costs above map perfectly to these 237 points. 

Below is the sprint-by-sprint cumulative cost breakdown for the **Organic Mode** (Cost per SP: ~₹22,920):

| Sprint | Story Points | Sprint Cost | Cumulative Cost |
| :--- | :--- | :--- | :--- |
| **Sprint 1** | 35 SP | ₹8,02,194 | **₹8,02,194** |
| **Sprint 2** | 39 SP | ₹8,93,873 | **₹16,96,068** |
| **Sprint 3** | 41 SP | ₹9,39,713 | **₹26,35,781** |
| **Sprint 4** | 40 SP | ₹9,16,793 | **₹35,52,574** |
| **Sprint 5** | 41 SP | ₹9,39,713 | **₹44,92,287** |
| **Sprint 6** | 41 SP | ₹9,39,713 | **₹54,32,000** |

*(Note: If calculated using Semi-Detached or Embedded mode, the proportional cost per sprint scales up accordingly relative to the totals above).*
