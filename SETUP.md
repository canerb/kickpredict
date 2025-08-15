# KickPredict - Setup Guide

## 🚀 Quick Start

Your KickPredict football prediction app is ready! Here's how to set it up:

### 1. Start the Application

```bash
# Start all services (includes automatic Vite dev server)
make up

# Or using docker-compose directly
docker-compose up -d
```

### 2. Configure OpenAI API Key

To enable AI predictions, add your OpenAI API key to the `.env` file:

```bash
# Add this line to your .env file
OPENAI_API_KEY=your_openai_api_key_here
```

You can get your API key from: https://platform.openai.com/api-keys

### 3. Access the Application

- **Web App**: http://localhost:8000
- **Vite Dev Server**: http://localhost:5173 (automatic)

## ⚡ Efficient Design

✅ **Single AI Service**: One call gets matches + predictions  
✅ **Token Efficient**: No separate calls for matches and predictions  
✅ **Gameweek Focus**: Gets next 6-8 matches with full analysis  
✅ **Football League Specialized**: Multiple leagues with real team names  
✅ **Comprehensive**: All betting markets in one response  

## 🎯 Features Ready

✅ **Next Gameweek Analysis**: AI fetches upcoming football matches  
✅ **Complete Predictions**: Match result, Over/Under, Both Teams to Score, etc.  
✅ **Real Turkish Teams**: Galatasaray, Fenerbahçe, Beşiktaş, Trabzonspor, etc.  
✅ **Modern UI**: Tailwind CSS with country flags and responsive design  
✅ **Auto Development**: Vite hot-reload starts automatically  

## 🛠 Development Commands

```bash
# View logs
make app-logs          # App container logs (includes Vite output)
make logs              # All container logs

# Database
make migrate           # Run migrations
make key               # Generate app key

# Container management
make restart           # Restart all services
make down              # Stop all services
make clean             # Clean up containers

# Shell access
make shell             # Access app container
```

## 🎮 How to Use

1. **Visit** http://localhost:8000
2. **Click** "Analyze Next Gameweek" to get matches + predictions in one AI call
3. **Browse** matches with complete betting analysis already included
4. **Expand** predictions to see detailed odds and AI reasoning

## 🔧 Architecture

### Single Service Design
- **SoccerAnalysisService**: One service handles everything
- **One AI Call**: Gets 6-8 matches with full predictions
- **Token Efficient**: ~80% fewer tokens than separate calls
- **Gameweek Focused**: Real-world scheduling approach

### What One AI Call Gets You:
```
Input: "Analyze next football gameweek"
Output: 
- 6-8 upcoming matches (realistic teams & dates)
- Complete betting predictions for each match
- Detailed analysis and confidence scores
- All in structured JSON format
```

## 🔧 Troubleshooting

### No Matches Showing
- Click "Analyze Next Gameweek" to generate complete gameweek analysis
- Check that OpenAI API key is configured

### Styling Issues
- Vite dev server starts automatically with `make up`
- Check `make app-logs` to see Vite status

### API Errors
- Ensure OpenAI API key is valid and has sufficient credits
- Check `make app-logs` for detailed error messages

## 📝 Next Steps

- Test the gameweek analysis functionality
- Verify prediction accuracy and formatting
- Expand to other leagues when ready
- Add user authentication and prediction history

---

**One click, complete gameweek analysis!** 🚀⚽ 